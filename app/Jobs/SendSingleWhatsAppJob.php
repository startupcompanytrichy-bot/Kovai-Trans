<?php

namespace App\Jobs;

use App\Models\VehicleReminderSend;
use App\Models\WhatsAppHistory;
use App\Models\WhatsAppReminderContact;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * SendSingleWhatsAppJob
 *
 * Sends a single WhatsApp message for one vehicle-document + one contact.
 * Dispatched by SendDocumentReminderJob — one job per contact per document.
 *
 * Queue:   whatsapp  (database driver)
 * Retries: 3, back-off: 60s → 300s → 600s
 * Timeout: 60 seconds
 *
 * On success → updates vehicle_reminder_sends + whatsapp_histories to "sent"
 * On failure → marks both records "failed"
 */
class SendSingleWhatsAppJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;
    public array $backoff = [60, 300, 600];

    public function __construct(
        public readonly int    $sendRecordId,
        public readonly int    $historyId,
        public readonly int    $contactId,
        public readonly string $contactNumber,
        public readonly string $message,
    ) {}

    // ── Handle ────────────────────────────────────────────────────────────────

    public function handle(WhatsAppService $whatsappService): void
    {
        $sendRecord = $this->sendRecordId > 0 ? VehicleReminderSend::find($this->sendRecordId) : null;
        $history    = WhatsAppHistory::find($this->historyId);
        $contact    = $this->contactId > 0 ? WhatsAppReminderContact::find($this->contactId) : null;

        Log::info("[SendSingleWhatsAppJob] Sending to {$this->contactNumber}");

        $sent = $whatsappService->sendMessage($this->contactNumber, $this->message);
        $now  = now();

        if ($sent) {
            $sendRecord?->update(['send_status' => 'sent', 'sent_at' => $now]);
            $history?->update(['send_status' => 'sent', 'sent_at' => $now]);
            $contact?->update(['last_send_status' => 'sent', 'last_sent_at' => $now]);
            Log::info("[SendSingleWhatsAppJob] ✓ Sent to {$this->contactNumber}");
        } else {
            $err = 'WhatsApp send failed';
            $sendRecord?->update(['send_status' => 'failed', 'error_message' => $err]);
            $history?->update(['send_status' => 'failed', 'error_message' => $err]);
            $contact?->update(['last_send_status' => 'failed']);
            Log::error("[SendSingleWhatsAppJob] ✗ Failed for {$this->contactNumber}");

            // Throw to trigger retry
            throw new \RuntimeException("WhatsApp send failed for {$this->contactNumber}");
        }
    }

    // ── Permanently failed (all retries exhausted) ────────────────────────────

    public function failed(\Throwable $exception): void
    {
        Log::error("[SendSingleWhatsAppJob] Permanently failed for {$this->contactNumber}: " . $exception->getMessage());

        VehicleReminderSend::find($this->sendRecordId)?->update([
            'send_status'   => 'failed',
            'error_message' => 'All retries exhausted: ' . $exception->getMessage(),
        ]);

        WhatsAppHistory::find($this->historyId)?->update([
            'send_status'   => 'failed',
            'error_message' => 'All retries exhausted: ' . $exception->getMessage(),
        ]);
    }
}
