<?php

use App\Jobs\SendDocumentReminderJob;
use App\Jobs\SendEmiReminderJob;
use App\Models\Setting;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Scheduled Tasks ───────────────────────────────────────────────────────────
//
// Architecture:
//   Cron (every minute) → schedule:run
//     → at configured time → dispatches SendDocumentReminderJob to "default" queue
//       → that job creates DB records + dispatches SendSingleWhatsAppJob per contact
//          → queue:work --queue=whatsapp,default processes and sends via WhatsApp
//
// ─────────────────────────────────────────────────────────────────────────────

// EMI reminders — daily at 9:00 AM IST
Schedule::job(new SendEmiReminderJob)
    ->dailyAt('09:00')
    ->timezone('Asia/Kolkata')
    ->withoutOverlapping();

// Document expiry reminders — re-read send time every minute so Settings changes
// take effect without restarting the container.
Schedule::call(function () {
    $sendTime = Setting::getValue('whatsapp_send_time', '09:30');
    $now      = \Carbon\Carbon::now('Asia/Kolkata')->format('H:i');

    if ($now === $sendTime) {
        SendDocumentReminderJob::dispatch();
        \Illuminate\Support\Facades\Log::info("[Scheduler] SendDocumentReminderJob dispatched at {$now} IST.");
    }
})->everyMinute()->name('send-document-reminders')->withoutOverlapping();
