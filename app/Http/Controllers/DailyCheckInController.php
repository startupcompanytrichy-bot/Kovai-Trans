<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleEmi;
use App\Models\VehicleReminderConfig;
use App\Models\WhatsAppReminderContact;
use App\Models\VehicleReminderSend;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * DailyCheckIn Controller
 *
 * Handles the Daily Check In page which shows:
 * 1. Vehicle Document Tracker - Shows all active vehicles with editable expiry dates
 * 2. Vehicle EMI Records - Shows all EMI payment records
 *
 * Uses the existing Settings flow:
 *   - Message Templates (message_templates) → message content with placeholders
 *   - Vehicle Reminder Configs (vehicle_reminder_configs) → links template + duration + time
 *   - WhatsApp Reminder Contacts (whatsapp_reminder_contacts) → recipients
 */
class DailyCheckInController extends Controller
{
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
     * Document types to check for expiry
     */
    protected $documentTypes = [
        'insurance_expiry_date' => 'Insurance',
        'fitness_expiry_date'   => 'Fitness',
        'puc_expiry_date'       => 'PUC',
        'national_permit_date'  => 'National Permit',
        'permit_expiry_date'    => 'Permit',
    ];

    /**
     * Display the Daily Check In page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $vehicles = Vehicle::where('status', 'active')
            ->orderBy('vehicle_name')
            ->get();

        $emis = VehicleEmi::with(['vehicle', 'payments'])
            ->where('is_deleted', false)
            ->orderBy('next_due_date')
            ->get();

        // Load reminder configs and active contacts from Settings
        $reminderConfigs = VehicleReminderConfig::with('template')->get();
        $contacts = WhatsAppReminderContact::where('is_active', true)->get();

        $today = Carbon::now()->startOfDay();

        // Pre-calculate reminder data for each vehicle/document
        $reminderData = [];
        foreach ($vehicles as $vehicle) {
            $reminderData[$vehicle->id] = [];
            foreach ($this->documentTypes as $field => $label) {
                $expiryDate = $vehicle->{$field} ? Carbon::parse($vehicle->{$field}) : null;
                if ($expiryDate) {
                    $daysRemaining = (int) $today->diffInDays($expiryDate, false);
                    $matchingConfig = $this->findMatchingConfig($reminderConfigs, $daysRemaining);
                    $alreadySent = VehicleReminderSend::where('vehicle_id', $vehicle->id)
                        ->where('document_type', $field)
                        ->where('expiry_date', $expiryDate->format('Y-m-d'))
                        ->where('send_status', 'sent')
                        ->exists();

                    $reminderData[$vehicle->id][$field] = [
                        'days_remaining' => $daysRemaining,
                        'config' => $matchingConfig,
                        'already_sent' => $alreadySent,
                        'expiry_date' => $expiryDate->format('Y-m-d'),
                    ];
                }
            }
        }

        return view('DailyCheckIn.index', compact('vehicles', 'emis', 'reminderConfigs', 'contacts', 'reminderData'));
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
     * Send WhatsApp reminder for a specific vehicle document
     *
     * Uses the message from the matching VehicleReminderConfig (copied from
     * Message Template when config was created).
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendReminder(Request $request)
    {
        $request->validate([
            'vehicle_id'    => 'required|exists:vehicles,id',
            'document_type' => 'required|in:insurance_expiry_date,fitness_expiry_date,puc_expiry_date,national_permit_date,permit_expiry_date',
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        $field = $request->document_type;
        $expiryDate = $vehicle->{$field} ? Carbon::parse($vehicle->{$field}) : null;

        if (!$expiryDate) {
            return response()->json(['success' => false, 'message' => 'No expiry date set for this document.']);
        }

        $today = Carbon::now()->startOfDay();
        $daysRemaining = (int) $today->diffInDays($expiryDate, false);

        // Find matching config
        $configs = VehicleReminderConfig::with('template')->get();
        $matchingConfig = $this->findMatchingConfig($configs, $daysRemaining);

        if (!$matchingConfig) {
            return response()->json(['success' => false, 'message' => 'No matching reminder config found. Please add a config in Settings → Vehicle Reminder.']);
        }

        // Document type labels
        $docLabel = $this->documentTypes[$field] ?? $field;

        // Build message using the template from config
        $message = $this->buildMessage($vehicle, $docLabel, $expiryDate, $daysRemaining, $matchingConfig);

        // Get active contacts
        $contacts = WhatsAppReminderContact::where('is_active', true)->get();
        if ($contacts->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No active WhatsApp contacts configured.']);
        }

        $whatsappService = app(WhatsAppService::class);
        $allSent = true;
        $lastError = null;

        // Send to all active contacts
        foreach ($contacts as $contact) {
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
                'created_by'     => auth()->id(),
            ]);

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
            } else {
                $allSent = false;
                $lastError = 'Failed to send to ' . $contact->mobile;
                $sendRecord->update([
                    'send_status'   => 'failed',
                    'error_message' => $lastError,
                ]);
                $contact->update([
                    'last_send_status' => 'failed',
                ]);
            }
        }

        if ($allSent) {
            return response()->json(['success' => true, 'message' => "Reminder sent to all " . $contacts->count() . " contacts."]);
        } else {
            return response()->json(['success' => false, 'message' => $lastError ?? 'Some messages failed to send.']);
        }
    }

    /**
     * Build the message using the template from config
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

        // Fallback message
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

    /**
     * Update vehicle document expiry dates
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateVehicle(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $validated = $request->validate([
            'insurance_expiry_date' => 'nullable|date',
            'fitness_expiry_date'   => 'nullable|date',
            'puc_expiry_date'       => 'nullable|date',
            'national_permit_date'  => 'nullable|date',
            'permit_expiry_date'    => 'nullable|date',
        ]);

        $vehicle->update($validated);

        return response()->json(['success' => true, 'message' => 'Vehicle dates updated successfully.']);
    }
}
