<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use App\Models\VehicleReminderConfig;
use App\Models\WhatsAppReminderContact;
use App\Models\VehicleReminderSend;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * Artisan Command: Send Document Expiry Reminders
 *
 * Uses the existing Settings flow:
 *   1. Message Templates (message_templates) - message content with placeholders
 *   2. Vehicle Reminder Configs (vehicle_reminder_configs) - links template + duration + time
 *   3. WhatsApp Reminder Contacts (whatsapp_reminder_contacts) - recipients
 *
 * Duration options (from Settings form):
 *   - "Last 30 Days" → send when 30 days or less remaining
 *   - "Last 15 Days" → send when 15 days or less remaining
 *   - "Last 10 Days" → send when 10 days or less remaining
 *   - "Last 5 Days"  → send when 5 days or less remaining
 *   - "Last 1 Day"   → send when 1 day or less remaining
 *
 * Placeholders in message template:
 *   - {{vehicle_number}}  → e.g. "TN 47 AY 4817"
 *   - {{document_type}}   → e.g. "Insurance", "Fitness", "PUC"
 *   - {{expiry_date}}     → e.g. "15 Aug 2026"
 *   - {{days_remaining}}  → e.g. "10" (negative if overdue)
 *
 * Usage:
 *   php artisan vehicle:send-document-reminders
 *
 * Schedule (in Kernel.php):
 *   Runs daily at 9:00 AM IST
 */
class SendDocumentReminders extends Command
{
    protected $signature = 'vehicle:send-document-reminders';

    protected $description = 'Send WhatsApp reminders for vehicle document expiries based on reminder configs';

    /**
     * Document types to check for expiry
     * Maps database column → display label
     */
    protected $documentTypes = [
        'insurance_expiry_date' => 'Insurance',
        'fitness_expiry_date'   => 'Fitness',
        'puc_expiry_date'       => 'PUC',
        'national_permit_date'  => 'National Permit',
        'permit_expiry_date'    => 'Permit',
    ];

    /**
     * Duration options from Settings form → days mapping
     */
    protected $durationDays = [
        'Last 30 Days' => 30,
        'Last 15 Days' => 15,
        'Last 10 Days' => 10,
        'Last 5 Days'  => 5,
        'Last 1 Day'   => 1,
    ];

    /**
     * Execute the command
     *
     * @return int Exit code
     */
    public function handle()
    {
        $this->info('Starting vehicle document reminder check...');
        $this->newLine();

        $today = Carbon::now()->startOfDay();

        // Get all vehicle reminder configs (from Settings → Vehicle Reminder)
        $configs = VehicleReminderConfig::with('template')->get();

        if ($configs->isEmpty()) {
            $this->warn('No vehicle reminder configs found. Please add configs in Settings → Vehicle Reminder.');
            return Command::SUCCESS;
        }

        // Get all active WhatsApp contacts (from Settings → Reminder Contacts)
        $contacts = WhatsAppReminderContact::where('is_active', true)->get();

        if ($contacts->isEmpty()) {
            $this->error('No active WhatsApp contacts found. Please add contacts in Settings → Reminder Contacts.');
            return Command::FAILURE;
        }

        $this->info("Found {$configs->count()} reminder config(s), {$contacts->count()} contact(s)");
        $this->newLine();

        $whatsappService = app(WhatsAppService::class);
        $vehicles = Vehicle::where('status', 'active')->get();
        $totalSent = 0;
        $totalFailed = 0;

        // Process each vehicle
        foreach ($vehicles as $vehicle) {
            // Check each document type
            foreach ($this->documentTypes as $field => $label) {
                $expiryDate = $vehicle->{$field} ? Carbon::parse($vehicle->{$field}) : null;
                if (!$expiryDate) continue;

                // Calculate days until expiry (negative = overdue)
                $daysRemaining = (int) $today->diffInDays($expiryDate, false);

                // Find matching config based on duration
                $matchingConfig = $this->findMatchingConfig($configs, $daysRemaining);
                if (!$matchingConfig) continue;

                // Skip if already sent today
                $alreadySentToday = VehicleReminderSend::where('vehicle_id', $vehicle->id)
                    ->where('document_type', $field)
                    ->whereDate('sent_at', $today->toDateString())
                    ->where('send_status', 'sent')
                    ->exists();

                if ($alreadySentToday) {
                    $this->line("  Skip: {$vehicle->vehicle_number} - {$label} (already sent today)");
                    continue;
                }

                // Build message using the template from config
                $message = $this->buildMessage($vehicle, $label, $expiryDate, $daysRemaining, $matchingConfig);

                $this->info("  Processing: {$vehicle->vehicle_number} - {$label} ({$daysRemaining} days remaining)");

                // Send to all active contacts
                foreach ($contacts as $contact) {
                    // Create tracking record
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
                        'created_by'     => null,
                    ]);

                    // Send via WhatsApp
                    $sent = $whatsappService->sendMessage($contact->mobile, $message);

                    if ($sent) {
                        $sendRecord->update([
                            'send_status' => 'sent',
                            'sent_at'     => now(),
                        ]);
                        $contact->update([
                            'last_send_status' => 'sent',
                            'last_sent_at'     => now(),
                        ]);
                        $totalSent++;
                        $this->line("    ✓ Sent to {$contact->name} ({$contact->mobile})");
                    } else {
                        $sendRecord->update([
                            'send_status'   => 'failed',
                            'error_message' => 'WhatsApp send failed',
                        ]);
                        $totalFailed++;
                        $this->error("    ✗ Failed to send to {$contact->name} ({$contact->mobile})");
                    }
                }
            }
        }

        $this->newLine();
        $this->info("Completed: {$totalSent} messages sent, {$totalFailed} failed");
        return Command::SUCCESS;
    }

    /**
     * Find matching config based on days remaining
     *
     * Duration options from Settings form:
     *   "Last 30 Days" → matches when days_remaining <= 30
     *   "Last 15 Days" → matches when days_remaining <= 15
     *   "Last 10 Days" → matches when days_remaining <= 10
     *   "Last 5 Days"  → matches when days_remaining <= 5
     *   "Last 1 Day"   → matches when days_remaining <= 1
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $configs
     * @param  int  $daysRemaining
     * @return VehicleReminderConfig|null
     */
    protected function findMatchingConfig($configs, $daysRemaining)
    {
        foreach ($configs as $config) {
            $maxDays = $this->durationDays[$config->duration] ?? null;
            if ($maxDays !== null && $daysRemaining <= $maxDays) {
                return $config;
            }
        }
        return null;
    }

    /**
     * Build the message using the template from config
     *
     * The message is stored in vehicle_reminder_configs.message (copied from
     * message_templates.message when config was created/updated).
     *
     * Supported placeholders:
     *   - {{vehicle_number}}  → Vehicle registration number
     *   - {{document_type}}   → Document type label
     *   - {{expiry_date}}     → Formatted expiry date
     *   - {{days_remaining}}  → Days until expiry (negative if overdue)
     *
     * @param  Vehicle  $vehicle
     * @param  string  $docLabel
     * @param  Carbon  $expiryDate
     * @param  int  $daysRemaining
     * @param  VehicleReminderConfig  $config
     * @return string
     */
    protected function buildMessage($vehicle, $docLabel, $expiryDate, $daysRemaining, $config)
    {
        // Use the message from the config (copied from Message Template)
        if ($config && $config->message) {
            return str_replace(
                [
                    '{{vehicle_number}}',
                    '{{document_type}}',
                    '{{expiry_date}}',
                    '{{days_remaining}}',
                ],
                [
                    $vehicle->vehicle_number,
                    $docLabel,
                    $expiryDate->format('d M Y'),
                    $daysRemaining,
                ],
                $config->message
            );
        }

        // Fallback: build default message if no template found
        if ($daysRemaining < 0) {
            $message = "🚨 *{$docLabel} Expired*\n\n";
            $message .= "Vehicle: {$vehicle->vehicle_number}\n";
            $message .= "{$docLabel} expired on {$expiryDate->format('d M Y')} (" . abs($daysRemaining) . " days ago)\n\n";
            $message .= "Please renew immediately to avoid penalties.";
        } elseif ($daysRemaining === 0) {
            $message = "⚠️ *{$docLabel} Expiry Today*\n\n";
            $message .= "Vehicle: {$vehicle->vehicle_number}\n";
            $message .= "{$docLabel} expires today ({$expiryDate->format('d M Y')})\n\n";
            $message .= "Please renew immediately.";
        } else {
            $message = "📋 *{$docLabel} Expiry Reminder*\n\n";
            $message .= "Vehicle: {$vehicle->vehicle_number}\n";
            $message .= "{$docLabel} expires on {$expiryDate->format('d M Y')} ({$daysRemaining} days left)\n\n";
            $message .= "Please renew before expiry.";
        }

        return $message;
    }
}
