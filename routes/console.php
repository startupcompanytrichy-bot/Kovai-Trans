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
//       → that job creates DB records and dispatches SendSingleWhatsAppJob
//          to "whatsapp" queue for every contact
//             → queue worker processes "whatsapp" queue, sends via WhatsApp service
//
// No bots, no direct HTTP calls in the scheduler.
// Everything goes through the database queue.
// ─────────────────────────────────────────────────────────────────────────────

// EMI reminders — daily at 9:00 AM IST, same reminder window as document reminders
Schedule::job(new SendEmiReminderJob)
    ->dailyAt('09:00')
    ->timezone('Asia/Kolkata')
    ->withoutOverlapping();

// Document expiry reminders — time from Settings → WhatsApp Message Config
// Falls back to 09:30 if not set.
// Dispatches SendDocumentReminderJob → which queues individual SendSingleWhatsAppJob per message.
$sendTime = Setting::getValue('whatsapp_send_time', '09:30');

Schedule::job(new SendDocumentReminderJob)
    ->dailyAt($sendTime)
    ->timezone('Asia/Kolkata')
    ->withoutOverlapping()
    ->onOneServer();
