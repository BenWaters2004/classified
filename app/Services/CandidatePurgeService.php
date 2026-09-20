<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class CandidatePurgeService
{
    /**
     * Permanently purge all locally stored information for one candidate.
     */
    public function purge(int $userId): array
    {
        $candidate = DB::table('users')
            ->leftJoin(
                'organisations',
                'organisations.id',
                '=',
                'users.organisationID'
            )
            ->where('users.id', $userId)
            ->where('users.userType', 'applicant')
            ->select([
                'users.id',
                'users.firstName',
                'users.lastName',
                'users.email',
                'users.completedDate',
                'users.organisationID',
                'organisations.organisationName',
            ])
            ->first();

        if (!$candidate) {
            throw new RuntimeException(
                "Candidate {$userId} could not be found."
            );
        }

        /*
         |--------------------------------------------------------------------------
         | Collect related IDs
         |--------------------------------------------------------------------------
         */

        $applicationIds = $this->pluckIds(
            'applications',
            'userID',
            $userId
        );

        $bpssApplicationIds = $this->pluckIds(
            'bpss_applications',
            'userID',
            $userId
        );

        $verificationRecordIds = $this->pluckIds(
            'bpss_vr',
            'userID',
            $userId
        );

        /*
         |--------------------------------------------------------------------------
         | Delete physical files first
         |--------------------------------------------------------------------------
         |
         | Files cannot participate in a database transaction.
         |
         | Deleting them first means that if a DB deletion later fails, the
         | candidate remains eligible and the purge can be retried next Monday.
         |
         */

        $this->deleteCandidateFiles(
            $userId,
            $applicationIds
        );

        /*
         |--------------------------------------------------------------------------
         | Delete database records
         |--------------------------------------------------------------------------
         */

        DB::transaction(function () use (
            $candidate,
            $userId,
            $applicationIds,
            $bpssApplicationIds,
            $verificationRecordIds
        ) {

            /*
             |--------------------------------------------------------------------------
             | BPSS Verification Record
             |--------------------------------------------------------------------------
             */

            if (!empty($verificationRecordIds)) {

                $this->deleteWhereIn(
                    'bpss_vr_references_forms',
                    'verificationRecordID',
                    $verificationRecordIds
                );

                $this->deleteWhereIn(
                    'bpss_vr_references_log',
                    'verificationRecordID',
                    $verificationRecordIds
                );

                $this->deleteWhereIn(
                    'bpss_vr_references',
                    'verificationRecordID',
                    $verificationRecordIds
                );

                $this->deleteWhereIn(
                    'bpss_vr_identity_documents',
                    'verificationRecordID',
                    $verificationRecordIds
                );

                $this->deleteWhereIn(
                    'bpss_vr',
                    'id',
                    $verificationRecordIds
                );
            }

            /*
             |--------------------------------------------------------------------------
             | BPSS Application
             |--------------------------------------------------------------------------
             */

            if (!empty($bpssApplicationIds)) {

                $bpssChildTables = [
                    'bpss_application_employment_history',
                    'bpss_application_extra_nationalities',
                    'bpss_application_passports',
                    'bpss_application_personal_referee',
                    'bpss_application_unemployment',
                ];

                foreach ($bpssChildTables as $table) {
                    $this->deleteWhereIn(
                        $table,
                        'applicationID',
                        $bpssApplicationIds
                    );
                }

                $this->deleteWhereIn(
                    'bpss_applications',
                    'id',
                    $bpssApplicationIds
                );
            }

            /*
             |--------------------------------------------------------------------------
             | DBS / Main Application
             |--------------------------------------------------------------------------
             */

            if (!empty($applicationIds)) {

                $applicationChildTables = [
                    'application_extra_names',
                    'application_previous_addresses',
                    'application_supporting_documents',
                ];

                foreach ($applicationChildTables as $table) {
                    $this->deleteWhereIn(
                        $table,
                        'applicationID',
                        $applicationIds
                    );
                }

                $this->deleteWhereIn(
                    'applications',
                    'id',
                    $applicationIds
                );
            }

            /*
             |--------------------------------------------------------------------------
             | Direct candidate records
             |--------------------------------------------------------------------------
             */

            $this->deleteWhere(
                'candidateForms',
                'userID',
                $userId
            );

            $this->deleteWhere(
                'collins_approvals',
                'userID',
                $userId
            );

            $this->deleteWhere(
                'new_applicant_documents',
                'userID',
                $userId
            );

            /*
             * Yoti uses userId rather than userID in the current code.
             */
            $this->deleteWhere(
                'Yoti',
                'userId',
                $userId
            );

            /*
             * MFA table - tolerate either naming convention.
             */
            $this->deleteUsingFirstExistingColumn(
                'users_mfa',
                ['userID', 'user_id', 'userid'],
                $userId
            );

            /*
             |--------------------------------------------------------------------------
             | Other locally stored candidate information
             |--------------------------------------------------------------------------
             */

            /*
             * Notifications contain the candidate's name/email inside their
             * title/body as well as relatedUserId.
             */
            $this->deleteWhere(
                'notifications',
                'relatedUserId',
                $userId
            );

            /*
             * Legacy signature information.
             */
            $this->deleteUsingFirstExistingColumn(
                'user_signatures',
                ['userid', 'userID', 'user_id'],
                $userId
            );

            /*
             * Normally applicants will not have these, but removing them makes
             * the purge complete if they do.
             */
            $this->deleteWhere(
                'user_roles',
                'userID',
                $userId
            );

            $this->deleteWhere(
                'user_organisations',
                'userID',
                $userId
            );

            /*
             * Destroy active DB sessions if the application uses database
             * sessions.
             */
            $this->deleteUsingFirstExistingColumn(
                'sessions',
                ['user_id', 'userID', 'userid'],
                $userId
            );

            /*
             * Sanctum tokens if this candidate ever acquired any.
             */
            if (
                Schema::hasTable('personal_access_tokens')
                && Schema::hasColumn(
                    'personal_access_tokens',
                    'tokenable_id'
                )
            ) {
                $query = DB::table('personal_access_tokens')
                    ->where('tokenable_id', $userId);

                if (
                    Schema::hasColumn(
                        'personal_access_tokens',
                        'tokenable_type'
                    )
                ) {
                    $query->where(
                        'tokenable_type',
                        'App\\Models\\User'
                    );
                }

                $query->delete();
            }

            /*
             * Old Laravel password-reset records are indexed by email.
             */
            if (
                !empty($candidate->email)
                && Schema::hasTable('password_resets')
                && Schema::hasColumn(
                    'password_resets',
                    'email'
                )
            ) {
                DB::table('password_resets')
                    ->where('email', $candidate->email)
                    ->delete();
            }

            /*
             |--------------------------------------------------------------------------
             | User last
             |--------------------------------------------------------------------------
             */

            DB::table('users')
                ->where('id', $userId)
                ->where('userType', 'applicant')
                ->delete();
        });

        return [
            'id'               => $candidate->id,
            'firstName'        => $candidate->firstName,
            'lastName'         => $candidate->lastName,
            'email'            => $candidate->email,
            'organisationID'   => $candidate->organisationID,
            'organisationName' => $candidate->organisationName,
            'completedDate'    => $candidate->completedDate,
        ];
    }

    /**
     * Delete physical candidate documents.
     */
    private function deleteCandidateFiles(
        int $userId,
        array $applicationIds
    ): void {
        /*
         * Candidate supporting documents.
         */
        if (
            !empty($applicationIds)
            && Schema::hasTable(
                'application_supporting_documents'
            )
        ) {
            $documents = DB::table(
                'application_supporting_documents'
            )
                ->whereIn(
                    'applicationID',
                    $applicationIds
                )
                ->get(['document_path']);

            foreach ($documents as $document) {
                if (empty($document->document_path)) {
                    continue;
                }

                $this->deleteStorageFile(
                    'supporting_documents/'
                    . basename($document->document_path)
                );
            }
        }

        /*
         * Files added by admins / supplied at invitation.
         */
        if (
            Schema::hasTable(
                'new_applicant_documents'
            )
        ) {
            $documents = DB::table(
                'new_applicant_documents'
            )
                ->where('userID', $userId)
                ->get(['document_path']);

            foreach ($documents as $document) {
                if (empty($document->document_path)) {
                    continue;
                }

                $this->deleteStorageFile(
                    'new_applicant_documents/'
                    . basename($document->document_path)
                );
            }
        }

        /*
         * Legacy user signature.
         */
        if (
            Schema::hasTable('user_signatures')
            && Schema::hasColumn(
                'user_signatures',
                'signature_path'
            )
        ) {
            $userColumn = $this->firstExistingColumn(
                'user_signatures',
                ['userid', 'userID', 'user_id']
            );

            if ($userColumn) {
                $signatures = DB::table('user_signatures')
                    ->where($userColumn, $userId)
                    ->get(['signature_path']);

                foreach ($signatures as $signature) {
                    if (empty($signature->signature_path)) {
                        continue;
                    }

                    $this->deleteStorageFile(
                        'user_signatures/'
                        . basename($signature->signature_path)
                    );
                }
            }
        }
    }

    private function deleteStorageFile(string $path): void
    {
        $disk = Storage::disk('public_images');

        if (!$disk->exists($path)) {
            return;
        }

        if (!$disk->delete($path)) {
            throw new RuntimeException(
                "Failed to delete candidate file: {$path}"
            );
        }
    }

    private function pluckIds(
        string $table,
        string $column,
        $value
    ): array {
        if (
            !Schema::hasTable($table)
            || !Schema::hasColumn($table, $column)
        ) {
            return [];
        }

        return DB::table($table)
            ->where($column, $value)
            ->pluck('id')
            ->map(function ($id) {
                return (int) $id;
            })
            ->values()
            ->all();
    }

    private function deleteWhere(
        string $table,
        string $column,
        $value
    ): int {
        if (
            !Schema::hasTable($table)
            || !Schema::hasColumn($table, $column)
        ) {
            return 0;
        }

        return DB::table($table)
            ->where($column, $value)
            ->delete();
    }

    private function deleteWhereIn(
        string $table,
        string $column,
        array $values
    ): int {
        if (
            empty($values)
            || !Schema::hasTable($table)
            || !Schema::hasColumn($table, $column)
        ) {
            return 0;
        }

        return DB::table($table)
            ->whereIn($column, $values)
            ->delete();
    }

    private function deleteUsingFirstExistingColumn(
        string $table,
        array $columns,
        $value
    ): int {
        $column = $this->firstExistingColumn(
            $table,
            $columns
        );

        if (!$column) {
            return 0;
        }

        return DB::table($table)
            ->where($column, $value)
            ->delete();
    }

    private function firstExistingColumn(
        string $table,
        array $columns
    ): ?string {
        if (!Schema::hasTable($table)) {
            return null;
        }

        foreach ($columns as $column) {
            if (Schema::hasColumn($table, $column)) {
                return $column;
            }
        }

        return null;
    }
}