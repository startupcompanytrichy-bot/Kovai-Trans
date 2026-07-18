<?php

namespace App\Jobs;

use App\Models\Setting;
use App\Models\Vehicle;
use App\Models\WhatsAppReminderContact;
use App\Models\VehicleReminderSend;
use App\Models\WhatsAppHistory;
use App\Traits\BuildsReminderMessage;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * SendDocumentReminderJob  (Prep / Orchestrator Job)
 *
 * Triggered daily at the configured send time (Settings → WhatsApp Message Config).
 * Also triggered immediately when the user saves new config in Settings.
 *
 * For every active vehicle × 5 document types:
 *   1. Reads "Days Before Expiry" from Settings (whatsapp_reminder_days).
 *   2. Sends if daysRemaining <= reminder_days (including overdue).
 *   3. Skips if already sent today.
 *   4. Creates pending DB records + dispatches SendSingleWhatsAppJob per contact.
 *
 * Does NOT call WhatsApp directly — that is done by SendSingleWhatsAppJob.
 */
class SendDocumentReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use BuildsReminderMessage;

    public int $tries   = 1;
    public int $timeout = 120;

    protected array $documentTypes = [
        'insurance_expiry_date' => 'Insurance',
        'fitness_expiry_date'   => 'Fitness',
        'puc_expiry_date'       => 'PUC',
        'national_permit_date'  => 'National Permit',
        'permit_expiry_date'    => 'Permit',
    ];

    public function handle(): void
    {
        // Read the reminder window from Settings (e.g. 3 = send 3 days before expiry)
        $reminderDays = (int) Setting::getValue('whatsapp_reminder_days', 15);

        Log::info("[SendDocumentReminderJob] Started — reminder window: {$reminderDays} days.");

        $contacts = WhatsAppReminderContact::where('is_active', true)->get();

        if ($contacts->isEmpty()) {
            Log::warning('[SendDocumentReminderJob] No active contacts — aborting.');
            return;
        }

        $today      = Carbon::now()->startOfDay();
        $vehicles   = Vehicle::where('status', 'active')->get();
        $dispatched = 0;
        $skipped    = 0;

        foreach ($vehicles as $vehicle) {
            foreach ($this->documentTypes as $field => $label) {
                $expiryDate = $vehicle->{$field} ? Carbon::parse($vehicle->{$field}) : null;
                if (!$expiryDate) continue;

                $daysRemaining = (int) $today->diffInDays($expiryDate, false);

                // Only send when within the configured reminder window (or overdue)
                if ($daysRemaining > $reminderDays) continue;

                // Skip if already sent today for this vehicle + doc + expiry
                $alreadySentToday = VehicleReminderSend::where('vehicle_id', $vehicle->id)
                    ->where('document_type', $field)
                    ->where('expiry_date', $expiryDate->format('Y-m-d'))
                    ->whereDate('sent_at', $today->toDateString())
                    ->where('send_status', 'sent')
                    ->exists();

                if ($alreadySentToday) {
                    $skipped++;
                    Log::info("[SendDocumentReminderJob] Skip (dup): {$vehicle->vehicle_number} — {$label}");
                    continue;
                }

                // Build message (no config needed — uses trait fallback)
                $message = $this->buildMessage($vehicle, $label, $expiryDate, $daysRemaining, null);

                foreach ($contacts as $contact) {
                    $sendRecord = VehicleReminderSend::create([
                        'company_id'     => $vehicle->company_id,
                        'branch_id'      => $vehicle->branch_id,
                        'vehicle_id'     => $vehicle->id,
                        'contact_id'     => $contact->id,
                        'config_id'      => null,
                        'document_type'  => $field,
                        'expiry_date'    => $expiryDate->format('Y-m-d'),
                        'days_remaining' => $daysRemaining,
                        'message'        => $message,
                        'contact_number' => $contact->mobile,
                        'send_status'    => 'pending',
                        'created_by'     => null,
                    ]);

                    $history = WhatsAppHistory::create([
                        'company_id'     => $vehicle->company_id,
                        'branch_id'      => $vehicle->branch_id,
                        'source'         => 'scheduled',
                        'vehicle_id'     => $vehicle->id,
                        'contact_id'     => $contact->id,
                        'document_type'  => $field,
                        'document_label' => $label,
                        'expiry_date'    => $expiryDate->format('Y-m-d'),
                        'days_remaining' => $daysRemaining,
                        'contact_number' => $contact->mobile,
                        'message'        => $message,
                        'send_status'    => 'pending',
                        'created_by'     => null,
                    ]);

                    SendSingleWhatsAppJob::dispatch(
                        $sendRecord->id,
                        $history->id,
                        $contact->id,
                        $contact->mobile,
                        $message
                    )->onQueue('whatsapp');

                    $dispatched++;
                    Log::info("[SendDocumentReminderJob] Queued: {$vehicle->vehicle_number} — {$label} → {$contact->mobile}");
                }
            }
        }

        Log::info("[SendDocumentReminderJob] Done — Dispatched: {$dispatched} | Skipped: {$skipped}");
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('[SendDocumentReminderJob] Failed: ' . $exception->getMessage());
    }
}
