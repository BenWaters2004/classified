<?php

namespace App\Console\Commands;

use App\Services\CandidatePurgeService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PurgeExpiredCandidates extends Command
{
    protected $signature = 'candidates:purge-expired
                            {--dry-run : Show candidates that would be purged without deleting anything}';

    protected $description =
        'Permanently purge candidates whose organisation data-retention period has expired.';

    private const REPORT_EMAIL =
        'screening@thinkbitgroup.co.uk';

    public function handle(
        CandidatePurgeService $purgeService
    ) {
        /*
         * Use UK time explicitly because this command is tied to
         * Monday 09:00 UK business time.
         */
        $now = Carbon::now('Europe/London');

        $candidates = DB::table('users')
            ->join(
                'organisations',
                'organisations.id',
                '=',
                'users.organisationID'
            )
            ->where(
                'users.userType',
                'applicant'
            )
            ->where(
                'users.completed',
                1
            )
            ->whereNotNull(
                'users.completedDate'
            )

            /*
             * Same eligibility rule as the dashboard warning:
             *
             * completedDate
             * +
             * max(organisation retention, 2 years)
             * <= now
             */
            ->whereRaw(
                'TIMESTAMPADD(
                    YEAR,
                    GREATEST(
                        COALESCE(
                            organisations.data_retention_years,
                            2
                        ),
                        2
                    ),
                    users.completedDate
                ) <= ?',
                [
                    $now->format(
                        'Y-m-d H:i:s'
                    )
                ]
            )

            ->select([
                'users.id',
                'users.firstName',
                'users.lastName',
                'users.email',
                'users.completedDate',
                'users.organisationID',
                'organisations.organisationName',
                'organisations.data_retention_years',

                DB::raw(
                    'TIMESTAMPADD(
                        YEAR,
                        GREATEST(
                            COALESCE(
                                organisations.data_retention_years,
                                2
                            ),
                            2
                        ),
                        users.completedDate
                    ) AS retentionExpiresAt'
                ),
            ])

            ->orderBy(
                'users.completedDate',
                'asc'
            )
            ->get();

        if ($candidates->isEmpty()) {
            $this->info(
                'No candidates are currently eligible for retention purge.'
            );

            return Command::SUCCESS;
        }

        /*
         |--------------------------------------------------------------------------
         | Dry run
         |--------------------------------------------------------------------------
         */

        if ($this->option('dry-run')) {

            $this->warn(
                'DRY RUN - no candidate data will be deleted.'
            );

            $rows = [];

            foreach ($candidates as $candidate) {
                $rows[] = [
                    $candidate->id,
                    trim(
                        $candidate->firstName
                        . ' '
                        . $candidate->lastName
                    ),
                    $candidate->email,
                    $candidate->organisationName,
                    $candidate->completedDate,
                    $candidate->retentionExpiresAt,
                ];
            }

            $this->table(
                [
                    'ID',
                    'Candidate',
                    'Email',
                    'Organisation',
                    'Completed',
                    'Retention expired',
                ],
                $rows
            );

            $this->info(
                $candidates->count()
                . ' candidate(s) would be purged.'
            );

            return Command::SUCCESS;
        }

        /*
         |--------------------------------------------------------------------------
         | Perform purge
         |--------------------------------------------------------------------------
         */

        $purged = [];
        $failed = [];

        foreach ($candidates as $candidate) {

            try {

                $result = $purgeService->purge(
                    (int) $candidate->id
                );

                $result['retentionYears'] = max(
                    2,
                    (int) (
                        $candidate->data_retention_years
                        ?? 2
                    )
                );

                $result['retentionExpiresAt'] =
                    $candidate->retentionExpiresAt;

                $result['purgedAt'] =
                    $now->format('Y-m-d H:i:s');

                $purged[] = $result;

                $this->info(
                    'Purged: '
                    . trim(
                        $candidate->firstName
                        . ' '
                        . $candidate->lastName
                    )
                    . ' <'
                    . $candidate->email
                    . '>'
                );

            } catch (\Throwable $e) {

                $failed[] = [
                    'id' => $candidate->id,
                    'firstName' =>
                        $candidate->firstName,
                    'lastName' =>
                        $candidate->lastName,
                    'email' =>
                        $candidate->email,
                    'organisationName' =>
                        $candidate->organisationName,
                ];

                Log::error(
                    'Candidate retention purge failed.',
                    [
                        'user_id' =>
                            $candidate->id,

                        'organisation_id' =>
                            $candidate->organisationID,

                        'exception' =>
                            get_class($e),

                        'message' =>
                            $e->getMessage(),
                    ]
                );

                $this->error(
                    'Failed to purge candidate ID '
                    . $candidate->id
                    . '. See Laravel log.'
                );
            }
        }

        /*
         |--------------------------------------------------------------------------
         | Email screening team
         |--------------------------------------------------------------------------
         */

        if (
            !empty($purged)
            || !empty($failed)
        ) {
            try {

                $reportEmail =
                    self::REPORT_EMAIL;

                Mail::send(
                    'email_templates.candidate_retention_purge_summary',
                    [
                        'purged' => $purged,
                        'failed' => $failed,
                        'runAt' => $now,
                    ],
                    function ($message) use (
                        $reportEmail,
                        $purged,
                        $failed
                    ) {

                        $message->to(
                            $reportEmail
                        );

                        if (!empty($failed)) {
                            $message->subject(
                                'Get ClassifIeD Data Retention Purge - '
                                . count($purged)
                                . ' purged, '
                                . count($failed)
                                . ' failed'
                            );
                        } else {
                            $message->subject(
                                'Get ClassifIeD Data Retention Purge - '
                                . count($purged)
                                . ' candidate'
                                . (
                                    count($purged) === 1
                                    ? ''
                                    : 's'
                                )
                                . ' purged'
                            );
                        }
                    }
                );

            } catch (\Throwable $e) {

                Log::error(
                    'Candidate retention purge completed but summary email failed.',
                    [
                        'purged_count' =>
                            count($purged),

                        'failed_count' =>
                            count($failed),

                        'message' =>
                            $e->getMessage(),
                    ]
                );

                $this->error(
                    'Purge completed, but the summary email could not be sent.'
                );

                return Command::FAILURE;
            }
        }

        /*
         |--------------------------------------------------------------------------
         | Result
         |--------------------------------------------------------------------------
         */

        $this->newLine();

        $this->info(
            count($purged)
            . ' candidate(s) successfully purged.'
        );

        if (!empty($failed)) {

            $this->error(
                count($failed)
                . ' candidate(s) could not be purged.'
            );

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}