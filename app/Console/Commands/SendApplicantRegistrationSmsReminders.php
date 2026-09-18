<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\ClickSendSmsService;

class SendApplicantRegistrationSmsReminders extends Command
{
    protected $signature = 'applicants:send-registration-sms-reminders';
    protected $description = 'Send SMS reminders 24h after applicant creation (business hours only)';

    public function handle(ClickSendSmsService $sms): int
    {
        $tz = config('app.timezone', 'Europe/London');
        $now = Carbon::now($tz);

        if (! $this->isBusinessTime($now)) {
            $this->info('Outside business hours. Skipping.');
            return self::SUCCESS;
        }

        $dueApplicants = DB::table('applicants')
            ->whereNull('sms_reminder_sent_at')
            ->whereNotNull('mobileNumberMain')
            ->where('createdOn', '<=', $now->copy()->subHours(24))
            ->limit(50)
            ->get();

        foreach ($dueApplicants as $applicant) {
            DB::transaction(function () use ($applicant, $sms, $tz) {
                $locked = DB::table('applicants')
                    ->where('id', $applicant->id)
                    ->lockForUpdate()
                    ->first();

                if (! $locked || $locked->sms_reminder_sent_at) {
                    return;
                }

                $organisation = DB::table('organisations')
                    ->select('organisationName')
                    ->where('id', $locked->organisationID)
                    ->first();

                $orgName = $organisation->organisationName ?? 'the requesting organisation';

                $message = "Hi {$locked->forename}, this is a reminder to complete your registration for background screening as requested by {$orgName}: {$this->registrationUrl($locked)}";

                $result = $sms->send(
                    to: $locked->mobileNumberMain,
                    body: $message
                );

                DB::table('applicants')->where('id', $locked->id)->update([
                    'sms_reminder_sent_at' => now($tz),
                    'sms_reminder_message_id' => $result['message_id'] ?? null,
                ]);
            });
        }

        $this->info("Processed {$dueApplicants->count()} applicants.");
        return self::SUCCESS;
    }

    private function isBusinessTime(Carbon $now): bool
    {
        if ($now->isWeekend()) return false;

        $start = $now->copy()->setTime(8, 30);
        $end   = $now->copy()->setTime(17, 30);

        return $now->betweenIncluded($start, $end);
    }

    private function registrationUrl(object $applicant): string
    {
        return url("/register/{$applicant->accessUrlCode}");
    }
}