<?php

namespace App\Services\Screening;

use App\Models\ScreeningCheckType;
use App\Models\ScreeningRequest;
use App\Models\ScreeningRequestCheck;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ScreeningRequestWriterService
{
    /**
     * Create/synchronise a V2 screening request from an existing
     * legacy applicants row.
     *
     * IMPORTANT:
     * - This never alters the legacy applicant.
     * - Existing V2 check statuses are never overwritten.
     * - Existing V2 checks are never deleted.
     * - Failures are logged rather than breaking the live application flow.
     */
    public function syncFromLegacyApplicant($applicantId)
    {
        $applicantId = (int) $applicantId;

        try {
            return DB::transaction(function () use ($applicantId) {

                $applicant = DB::table('applicants')
                    ->where('id', $applicantId)
                    ->first();

                if (!$applicant) {
                    Log::warning(
                        'Screening V2 dual-write skipped: applicant not found.',
                        [
                            'applicant_id' => $applicantId,
                        ]
                    );

                    return false;
                }

                $checkCodes = $this->legacyCheckCodes($applicant);

                /*
                 * Do not create an empty V2 screening request if this is
                 * some old/unsupported application type.
                 */
                if (empty($checkCodes)) {
                    Log::info(
                        'Screening V2 dual-write skipped: no recognised checks.',
                        [
                            'applicant_id' => $applicantId,
                        ]
                    );

                    return false;
                }

                /*
                 * One legacy applicants row represents one invitation /
                 * screening request.
                 */
                $screeningRequest = ScreeningRequest::where(
                    'applicant_id',
                    $applicantId
                )
                    ->orderBy('id', 'desc')
                    ->first();

                if (!$screeningRequest) {

                    $screeningRequest = ScreeningRequest::create([
                        'applicant_id' => $applicantId,

                        'user_id' =>
                            !empty($applicant->userID)
                                ? (int) $applicant->userID
                                : null,

                        'organisation_id' =>
                            (int) $applicant->organisationID,

                        'status' => 'pending_registration',

                        'created_by' =>
                            !empty($applicant->createdBy)
                                ? (int) $applicant->createdBy
                                : null,
                    ]);
                }

                /*
                 * If the request already exists but wasn't linked to the
                 * user previously, fill that in without touching status.
                 */
                if (
                    empty($screeningRequest->user_id) &&
                    !empty($applicant->userID)
                ) {
                    $screeningRequest->user_id =
                        (int) $applicant->userID;

                    $screeningRequest->save();
                }

                $checkTypes = ScreeningCheckType::whereIn(
                    'code',
                    $checkCodes
                )
                    ->where('active', 1)
                    ->get()
                    ->keyBy('code');

                foreach ($checkCodes as $checkCode) {

                    if (!$checkTypes->has($checkCode)) {

                        /*
                         * Do not break applicant creation because a seed
                         * record is accidentally missing.
                         */
                        Log::warning(
                            'Screening V2 check type missing during dual-write.',
                            [
                                'applicant_id' => $applicantId,
                                'check_code' => $checkCode,
                            ]
                        );

                        continue;
                    }

                    $checkType = $checkTypes->get($checkCode);

                    /*
                     * firstOrCreate is important here.
                     *
                     * If a V2 check already exists and has moved to
                     * submitted/completed/failed, we must NOT reset it
                     * back to requested.
                     */
                    ScreeningRequestCheck::firstOrCreate(
                        [
                            'screening_request_id' =>
                                $screeningRequest->id,

                            'check_type_id' =>
                                $checkType->id,
                        ],
                        [
                            'status' => 'requested',
                        ]
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Resolve Candidate Forms
                |--------------------------------------------------------------------------
                |
                | Build the V2 candidate sections required by the selected screening
                | checks. This does not affect the existing candidateForms table.
                |
                */

                app(
                    \App\Services\Screening\CandidateSectionResolver::class
                )->syncRequestForms(
                    $screeningRequest->id
                );

                return $screeningRequest->fresh([
                    'checks.checkType',
                ]);
            });

        } catch (Throwable $e) {

            /*
             * V2 is currently supplementary.
             *
             * A V2 issue must NEVER prevent the legacy applicant
             * invitation from being created.
             */
            Log::error(
                'Screening V2 dual-write failed.',
                [
                    'applicant_id' => $applicantId,
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            );

            return false;
        }
    }

    /**
     * Translate the existing applicant flags into V2 screening checks.
     */
    protected function legacyCheckCodes($applicant)
    {
        $checks = [];

        /*
         * Current DBS flow is Basic DBS.
         */
        if ((int) $applicant->dbsApplication === 1) {
            $checks[] = 'DBS_BASIC';
        }

        /*
         * BPSS revalidation is still a BPSS screening request.
         *
         * The onsite/offsite distinction is workflow metadata rather
         * than a separate screening product.
         */
        if (
            (int) $applicant->bpssApplication === 1 ||
            (int) $applicant->REVALonsite === 1 ||
            (int) $applicant->REVALoffsite === 1
        ) {
            $checks[] = 'BPSS';
        }

        /*
         * Legacy Yoti states:
         *
         * 1 = incomplete
         * 2 = complete
         * 3 = failed
         *
         * Anything > 0 means Yoti was requested.
         */
        if ((int) $applicant->useYoti > 0) {
            $checks[] = 'YOTI_IDV';
        }

        return array_values(
            array_unique($checks)
        );
    }
}