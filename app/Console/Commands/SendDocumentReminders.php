<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use App\Models\VehicleReminderConfig;
use App\Models\WhatsAppReminderContact;
use App\Models\VehicleReminderSend;
use App\Models\WhatsAppHistory;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * Artisan Command: Send Vehicle Document Expiry Reminders
 *
 * Runs daily at 12:00 noon IST (Asia/Kolkata).
 *
 * Logic:
 *   1. Load all VehicleReminderConfigs + active WhatsAppReminderContacts.
 *   2. For every active vehicle × 5 document types:
 *        - Calculate days until expiry.
 *        - Match a config whose duration threshold covers the remaining days.
 *        - Skip if a "sent" record already exists for today (same vehicle + doc + expiry).
 *        - Build message from config template (placeholders replaced).
 *        - Send to every active contact via WhatsAppService.
 *        - Log in vehicle_reminder_sends (detailed tracking) AND whatsapp_histories (audit log).
 *
 * Placeholders supported in message template:
 *   {{vehicle_number}}  {{document_type}}  {{expiry_date}}  {{days_remaining}}
 *
 * Usage:
 *   php artisan vehicle:send-document-reminders
 */
class SendDocumentReminders extends Command
{
    protected $signature = 'vehicle:send-document-reminders';

    protected $description = 'Send WhatsApp reminders for vehicle document expiries (runs daily at 12:00 noon)';

    /** DB column → display label */
    protected $documentTypes = [
        'insurance_expiry_date' => 'Insurance',
        'fitness_expiry_date'   => 'Fitness',
        'puc_expiry_date'       => 'PUC',
        'national_permit_date'  => 'National Permit',
        'permit_expiry_date'    => 'Permit',
    ];

    /** Duration label → days (from Settings form options) */
    protected $durationDays = [
        'Last 30 Days' => 30,
        'Last 15 Days' => 15,
        'Last 10 Days' => 10,
        'Last 5 Days'  => 5,
        'Last 1 Day'   => 1,
    ];

    public function handle(): int
    {
        $this->info('=== Vehicle Document Reminder — Daily Run ===');
        $this->newLine();

        $today = Carbon::now()->startOfDay();

        // Load configs and contacts once
        $configs = VehicleReminderConfig::with('template')->get();

        if ($configs->isEmpty()) {
            $this->warn('No vehicle reminder configs found. Add configs in Settings → Vehicle Reminder.');
            return Command::SUCCESS;
        }

        $contacts = WhatsAppReminderContact::where('is_active', true)->get();

        if ($contacts->isEmpty()) {
            $this->error('No active WhatsApp contacts. Add contacts in Settings → Reminder Contacts.');
            return Command::FAILURE;
        }

        $this->info("Configs: {$configs->count()}  |  Contacts: {$contacts->count()}");
        $this->newLine();

        $whatsappService = app(WhatsAppService::class);
        $vehicles        = Vehicle::where('status', 'active')->get();
        $totalSent       = 0;
        $totalFailed     = 0;
        $totalSkipped    = 0;

        foreach ($vehicles as $vehicle) {
            foreach ($this->documentTypes as $field => $label) {
                $expiryDate = $vehicle->{$field} ? Carbon::parse($vehicle->{$field}) : null;
                if (!$expiryDate) continue;

                $daysRemaining  = (int) $today->diffInDays($expiryDate, false);

                // Find best (tightest) matching config
                $matchingConfig = $this->findMatchingConfig($configs, $daysRemaining);
                if (!$matchingConfig) continue;

                // Skip if already sent today for this vehicle + doc + expiry date
                $alreadySentToday = VehicleReminderSend::where('vehicle_id', $vehicle->id)
                    ->where('document_type', $field)
                    ->where('expiry_date', $expiryDate->format('Y-m-d'))
                    ->whereDate('sent_at', $today->toDateString())
                    ->where('send_status', 'sent')
                    ->exists();

                if ($alreadySentToday) {
                    $this->line("  ⏭  Skip (already sent today): {$vehicle->vehicle_number} — {$label}");
                    $totalSkipped++;
                    continue;
                }

                $message = $this->buildMessage($vehicle, $label, $expiryDate, $daysRemaining, $matchingConfig);

                $this->info("  ▶  {$vehicle->vehicle_number} — {$label} ({$daysRemaining} days)");

                foreach ($contacts as $contact) {
                    // vehicle_reminder_sends
                    $sendRecord = VehicleReminderSend::create([
                        'company_id'     => $vehicle->company_id,
                        'branch_id'      => $vehicle->branch_id,
                        'vehicle_id'     => $vehicle->id,
                        'contact_id'     => $contact->id,
                        'config_id'      => $matchingConfig->id,
                        'document_type'  => $field,
                        'expiry_date'    => $expiryDate->format('Y-m-d'),
                        'days_remaining' => $daysRemaining,
                        'message'        => $message,
                        'contact_number' => $contact->mobile,
                        'send_status'    => 'pending',
                        'created_by'     => null, // automated
                    ]);

                    // whatsapp_histories
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

                    $sent = $whatsappService->sendMessage($contact->mobile, $message);
                    $now  = now();

                    if ($sent) {
                        $sendRecord->update(['send_status' => 'sent', 'sent_at' => $now]);
                        $history->update(['send_status' => 'sent', 'sent_at' => $now]);
                        $contact->update(['last_send_status' => 'sent', 'last_sent_at' => $now]);
                        $totalSent++;
                        $this->line("     ✓ {$contact->name} ({$contact->mobile})");
                    } else {
                        $err = 'WhatsApp send failed';
                        $sendRecord->update(['send_status' => 'failed', 'error_message' => $err]);
                        $history->update(['send_status' => 'failed', 'error_message' => $err]);
                        $totalFailed++;
                        $this->error("     ✗ {$contact->name} ({$contact->mobile})");
                    }
                }
            }
        }

        $this->newLine();
        $this->info("Done — Sent: {$totalSent}  |  Failed: {$totalFailed}  |  Skipped (dup): {$totalSkipped}");
        return Command::SUCCESS;
    }

    /**
     * Return the config with the tightest (smallest) matching duration window.
     * Example: daysRemaining=10, configs for 15 and 30 days → returns 15-day config.
     */
    protected function findMatchingConfig($configs, int $daysRemaining)
    {
        $best    = null;
        $bestMax = PHP_INT_MAX;

        foreach ($configs as $config) {
            $maxDays = $this->durationDays[$config->duration] ?? null;
            if ($maxDays !== null && $daysRemaining <= $maxDays && $maxDays < $bestMax) {
                $best    = $config;
                $bestMax = $maxDays;
            }
        }

        return $best;
    }

    /**
     * Build the reminder message using config template or fallback text.
     */
    protected function buildMessage(Vehicle $vehicle, string $docLabel, Carbon $expiryDate, int $daysRemaining, $config): string
    {
        if ($config && $config->message) {
            return str_replace(
                ['{{vehicle_number}}', '{{document_type}}', '{{expiry_date}}', '{{days_remaining}}'],
                [$vehicle->vehicle_number, $docLabel, $expiryDate->format('d M Y'), $daysRemaining],
                $config->message
            );
        }

        if ($daysRemaining < 0) {
            return "🚨 *{$docLabel} Expired*\n\n"
                . "Vehicle: {$vehicle->vehicle_number}\n"
                . "{$docLabel} expired on {$expiryDate->format('d M Y')} (" . abs($daysRemaining) . " days ago)\n\n"
                . "Please renew immediately to avoid penalties.";
        }

        if ($daysRemaining === 0) {
            return "⚠️ *{$docLabel} Expiry Today*\n\n"
                . "Vehicle: {$vehicle->vehicle_number}\n"
                . "{$docLabel} expires today ({$expiryDate->format('d M Y')})\n\n"
                . "Please renew immediately.";
        }

        return "📋 *{$docLabel} Expiry Reminder*\n\n"
            . "Vehicle: {$vehicle->vehicle_number}\n"
            . "{$docLabel} expires on {$expiryDate->format('d M Y')} ({$daysRemaining} days left)\n\n"
            . "Please renew before expiry.";
    }
}
