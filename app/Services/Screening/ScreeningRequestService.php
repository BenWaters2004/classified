<?php

namespace App\Services\Screening;

use App\Models\ScreeningRequest;
use Illuminate\Support\Facades\DB;

class ScreeningRequestService
{
    public const CHECK_DBS_BASIC = 'DBS_BASIC';
    public const CHECK_BPSS = 'BPSS';
    public const CHECK_YOTI = 'YOTI_IDV';

    /**
     * Resolve the screening configuration for a registered candidate.
     *
     * The new screening architecture is preferred when a screening_request
     * exists. Otherwise the service falls back to the existing legacy flags.
     *
     * This method is READ ONLY.
     */
    public function resolveForUser($userId)
    {
        $userId = (int) $userId;

        $request = ScreeningRequest::with([
            'checks.checkType',
        ])
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->first();

        if ($request) {
            return $this->formatV2Request($request);
        }

        return $this->resolveLegacyUser($userId);
    }

    /**
     * Resolve screening configuration for an applicant who has not
     * necessarily registered as a user yet.
     *
     * This method is READ ONLY.
     */
    public function resolveForApplicant($applicantId)
    {
        $applicantId = (int) $applicantId;

        $request = ScreeningRequest::with([
            'checks.checkType',
        ])
            ->where('applicant_id', $applicantId)
            ->orderBy('id', 'desc')
            ->first();

        if ($request) {
            return $this->formatV2Request($request);
        }

        return $this->resolveLegacyApplicant($applicantId);
    }

    /**
     * Return just the check codes for a registered candidate.
     */
    public function checkCodesForUser($userId)
    {
        $screening = $this->resolveForUser($userId);

        if (!$screening) {
            return [];
        }

        return $screening['check_codes'];
    }

    /**
     * Return just the check codes for an applicant.
     */
    public function checkCodesForApplicant($applicantId)
    {
        $screening = $this->resolveForApplicant($applicantId);

        if (!$screening) {
            return [];
        }

        return $screening['check_codes'];
    }

    /**
     * Determine whether a candidate has a particular requested check.
     */
    public function userHasCheck($userId, $checkCode)
    {
        return in_array(
            $checkCode,
            $this->checkCodesForUser($userId),
            true
        );
    }

    /**
     * Determine whether an applicant has a particular requested check.
     */
    public function applicantHasCheck($applicantId, $checkCode)
    {
        return in_array(
            $checkCode,
            $this->checkCodesForApplicant($applicantId),
            true
        );
    }

    /**
     * Resolve an existing user using the current legacy columns.
     */
    protected function resolveLegacyUser($userId)
    {
        $user = DB::table('users')
            ->select([
                'id',
                'organisationID',
                'dbsApplication',
                'bpssApplication',
                'REVALonsite',
                'REVALoffsite',
                'useYoti',
                'completed',
                'completedDate',
            ])
            ->where('id', $userId)
            ->first();

        if (!$user) {
            return null;
        }

        $checkCodes = $this->legacyCheckCodes($user);

        return [
            'source' => 'legacy',

            'request_id' => null,

            'applicant_id' => null,
            'user_id' => (int) $user->id,
            'organisation_id' => (int) $user->organisationID,

            'status' => !empty($user->completed)
                ? 'completed'
                : 'in_progress',

            'check_codes' => $checkCodes,

            'checks' => $this->legacyCheckDetails(
                $checkCodes,
                $user
            ),

            'legacy' => [
                'dbsApplication' => (int) $user->dbsApplication,
                'bpssApplication' => (int) $user->bpssApplication,
                'REVALonsite' => $user->REVALonsite === null
                    ? null
                    : (int) $user->REVALonsite,
                'REVALoffsite' => $user->REVALoffsite === null
                    ? null
                    : (int) $user->REVALoffsite,
                'useYoti' => $user->useYoti === null
                    ? null
                    : (int) $user->useYoti,
            ],
        ];
    }

    /**
     * Resolve an applicant using the current legacy columns.
     */
    protected function resolveLegacyApplicant($applicantId)
    {
        $applicant = DB::table('applicants')
            ->select([
                'id',
                'organisationID',
                'dbsApplication',
                'bpssApplication',
                'REVALonsite',
                'REVALoffsite',
                'useYoti',
            ])
            ->where('id', $applicantId)
            ->first();

        if (!$applicant) {
            return null;
        }

        $checkCodes = $this->legacyCheckCodes($applicant);

        return [
            'source' => 'legacy',

            'request_id' => null,

            'applicant_id' => (int) $applicant->id,
            'user_id' => null,
            'organisation_id' => (int) $applicant->organisationID,

            'status' => 'pending_registration',

            'check_codes' => $checkCodes,

            'checks' => $this->legacyCheckDetails(
                $checkCodes,
                $applicant
            ),

            'legacy' => [
                'dbsApplication' => (int) $applicant->dbsApplication,
                'bpssApplication' => (int) $applicant->bpssApplication,
                'REVALonsite' => $applicant->REVALonsite === null
                    ? null
                    : (int) $applicant->REVALonsite,
                'REVALoffsite' => $applicant->REVALoffsite === null
                    ? null
                    : (int) $applicant->REVALoffsite,
                'useYoti' => $applicant->useYoti === null
                    ? null
                    : (int) $applicant->useYoti,
            ],
        ];
    }

    /**
     * Convert an existing legacy record into the new check codes.
     *
     * This does NOT write anything to the database.
     */
    protected function legacyCheckCodes($record)
    {
        $checks = [];

        if ((int) $record->dbsApplication === 1) {
            $checks[] = self::CHECK_DBS_BASIC;
        }

        /*
         * Revalidation is currently part of the old BPSS workflow, so either
         * BPSS itself or one of the revalidation flags means BPSS should be
         * visible to the new screening architecture.
         */
        if (
            (int) $record->bpssApplication === 1 ||
            (int) $record->REVALonsite === 1 ||
            (int) $record->REVALoffsite === 1
        ) {
            $checks[] = self::CHECK_BPSS;
        }

        /*
         * Existing Yoti values:
         *
         * 0/null = not requested
         * 1      = incomplete
         * 2      = complete
         * 3      = failed
         *
         * Therefore any value above zero means Yoti formed part of the
         * screening request.
         */
        if ((int) $record->useYoti > 0) {
            $checks[] = self::CHECK_YOTI;
        }

        return array_values(array_unique($checks));
    }

    /**
     * Give legacy checks a structure matching the V2 check output.
     */
    protected function legacyCheckDetails(array $checkCodes, $record)
    {
        $checks = [];

        foreach ($checkCodes as $code) {
            $status = 'requested';

            if ($code === self::CHECK_YOTI) {
                $status = $this->legacyYotiStatus(
                    isset($record->useYoti)
                        ? (int) $record->useYoti
                        : 0
                );
            }

            $checks[] = [
                'id' => null,
                'code' => $code,
                'name' => $this->legacyCheckName($code),
                'provider' => $this->legacyCheckProvider($code),
                'category' => $this->legacyCheckCategory($code),
                'status' => $status,
                'provider_reference' => null,
                'provider_record_type' => null,
                'provider_record_id' => null,
                'metadata' => [],
            ];
        }

        return $checks;
    }

    /**
     * Convert a V2 screening request into the common structure used by
     * the candidate portal.
     */
    protected function formatV2Request(ScreeningRequest $request)
    {
        $checks = [];
        $checkCodes = [];

        foreach ($request->checks as $requestCheck) {
            $type = $requestCheck->checkType;

            /*
             * Protect the portal from an orphaned/invalid check type.
             */
            if (!$type) {
                continue;
            }

            $checkCodes[] = $type->code;

            $checks[] = [
                'id' => (int) $requestCheck->id,
                'code' => $type->code,
                'name' => $type->name,
                'provider' => $type->provider,
                'category' => $type->category,
                'status' => $requestCheck->status,

                'provider_reference' =>
                    $requestCheck->provider_reference,

                'provider_record_type' =>
                    $requestCheck->provider_record_type,

                'provider_record_id' =>
                    $requestCheck->provider_record_id === null
                        ? null
                        : (int) $requestCheck->provider_record_id,

                'metadata' =>
                    is_array($requestCheck->metadata_json)
                        ? $requestCheck->metadata_json
                        : [],
            ];
        }

        return [
            'source' => 'screening_v2',

            'request_id' => (int) $request->id,

            'applicant_id' => $request->applicant_id === null
                ? null
                : (int) $request->applicant_id,

            'user_id' => $request->user_id === null
                ? null
                : (int) $request->user_id,

            'organisation_id' => (int) $request->organisation_id,

            'status' => $request->status,

            'check_codes' => array_values(
                array_unique($checkCodes)
            ),

            'checks' => $checks,

            'legacy' => null,
        ];
    }

    protected function legacyYotiStatus($useYoti)
    {
        switch ((int) $useYoti) {
            case 2:
                return 'completed';

            case 3:
                return 'failed';

            case 1:
            default:
                return 'requested';
        }
    }

    protected function legacyCheckName($code)
    {
        switch ($code) {
            case self::CHECK_DBS_BASIC:
                return 'DBS Basic';

            case self::CHECK_BPSS:
                return 'BPSS';

            case self::CHECK_YOTI:
                return 'Digital Identity Verification';

            default:
                return $code;
        }
    }

    protected function legacyCheckProvider($code)
    {
        switch ($code) {
            case self::CHECK_DBS_BASIC:
                return 'dbs';

            case self::CHECK_YOTI:
                return 'yoti';

            case self::CHECK_BPSS:
            default:
                return 'internal';
        }
    }

    protected function legacyCheckCategory($code)
    {
        switch ($code) {
            case self::CHECK_DBS_BASIC:
                return 'criminal_record';

            case self::CHECK_YOTI:
                return 'identity';

            case self::CHECK_BPSS:
            default:
                return 'pre_employment';
        }
    }
}