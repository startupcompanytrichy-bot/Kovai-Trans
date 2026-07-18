<?php

namespace App\Http\Controllers;

use App\Jobs\SendSingleWhatsAppJob;
use App\Models\Setting;
use App\Models\Vehicle;
use App\Models\VehicleEmi;
use App\Models\VehicleReminderConfig;
use App\Models\WhatsAppReminderContact;
use App\Models\VehicleReminderSend;
use App\Models\WhatsAppHistory;
use App\Traits\BuildsReminderMessage;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * DailyCheckIn Controller
 *
 * Shows:
 *   1. Vehicle Document Tracker — editable expiry dates per vehicle
 *   2. Vehicle EMI Records
 *
 * All WhatsApp messages go through the queue (SendSingleWhatsAppJob).
 * Reminder window is driven by Settings → whatsapp_reminder_days.
 */
class DailyCheckInController extends Controller
{
    use BuildsReminderMessage;

    protected array $documentTypes = [
        'insurance_expiry_date' => 'Insurance',
        'fitness_expiry_date'   => 'Fitness',
        'puc_expiry_date'       => 'PUC',
        'national_permit_date'  => 'National Permit',
        'permit_expiry_date'    => 'Permit',
    ];

    // ── Index ─────────────────────────────────────────────────────────────────

    public function index()
    {
        $vehicles = Vehicle::where('status', 'active')->orderBy('vehicle_name')->get();

        $emis = VehicleEmi::with(['vehicle', 'payments'])
            ->where('is_deleted', false)
            ->orderBy('next_due_date')
            ->get();

        $reminderConfigs = VehicleReminderConfig::with('template')->get();
        $contacts        = WhatsAppReminderContact::where('is_active', true)->get();
        $today           = Carbon::now()->startOfDay();
        $reminderDays    = (int) Setting::getValue('whatsapp_reminder_days', 15);

        $reminderData = [];
        foreach ($vehicles as $vehicle) {
            $reminderData[$vehicle->id] = [];
            foreach ($this->documentTypes as $field => $label) {
                $expiryDate = $vehicle->{$field} ? Carbon::parse($vehicle->{$field}) : null;
                if (!$expiryDate) continue;

                $daysRemaining = (int) $today->diffInDays($expiryDate, false);
                $alreadySent   = VehicleReminderSend::where('vehicle_id', $vehicle->id)
                    ->where('document_type', $field)
                    ->where('expiry_date', $expiryDate->format('Y-m-d'))
                    ->where('send_status', 'sent')
                    ->exists();

                $reminderData[$vehicle->id][$field] = [
                    'days_remaining' => $daysRemaining,
                    'within_window'  => $daysRemaining <= $reminderDays,
                    'already_sent'   => $alreadySent,
                    'expiry_date'    => $expiryDate->format('Y-m-d'),
                ];
            }
        }

        return view('DailyCheckIn.index', compact(
            'vehicles', 'emis', 'reminderConfigs', 'contacts', 'reminderData', 'reminderDays'
        ));
    }

    // ── Update Vehicle Dates ──────────────────────────────────────────────────

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

        $changedFields = array_intersect_key($validated, $request->all());
        $vehicle->update($changedFields);

        // Auto-queue reminders for any updated date within the configured window
        $this->queueRemindersOnUpdate($vehicle, $changedFields);

        return response()->json(['success' => true, 'message' => 'Vehicle dates updated successfully.']);
    }

    // ── Auto-queue on date update ─────────────────────────────────────────────

    protected function queueRemindersOnUpdate(Vehicle $vehicle, array $fields): void
    {
        $today        = Carbon::now()->startOfDay();
        $contacts     = WhatsAppReminderContact::where('is_active', true)->get();
        $reminderDays = (int) Setting::getValue('whatsapp_reminder_days', 15);

        foreach ($fields as $field => $value) {
            if (!$value || !isset($this->documentTypes[$field])) continue;

            $expiryDate    = Carbon::parse($value);
            $daysRemaining = (int) $today->diffInDays($expiryDate, false);

            // Only trigger if within the configured window (or already overdue)
            if ($daysRemaining > $reminderDays) continue;

            $docLabel = $this->documentTypes[$field];
            $message  = $this->buildMessage($vehicle, $docLabel, $expiryDate, $daysRemaining, null);

            foreach ($contacts as $contact) {
                // Skip if already sent today
                $alreadySentToday = VehicleReminderSend::where('vehicle_id', $vehicle->id)
                    ->where('document_type', $field)
                    ->where('expiry_date', $expiryDate->format('Y-m-d'))
                    ->whereDate('sent_at', $today->toDateString())
                    ->where('send_status', 'sent')
                    ->exists();

                if ($alreadySentToday) continue;

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
                    'created_by'     => auth()->id(),
                ]);

                $history = WhatsAppHistory::create([
                    'company_id'     => $vehicle->company_id,
                    'branch_id'      => $vehicle->branch_id,
                    'source'         => 'update_trigger',
                    'vehicle_id'     => $vehicle->id,
                    'contact_id'     => $contact->id,
                    'document_type'  => $field,
                    'document_label' => $docLabel,
                    'expiry_date'    => $expiryDate->format('Y-m-d'),
                    'days_remaining' => $daysRemaining,
                    'contact_number' => $contact->mobile,
                    'message'        => $message,
                    'send_status'    => 'pending',
                    'created_by'     => auth()->id(),
                ]);

                SendSingleWhatsAppJob::dispatch(
                    $sendRecord->id, $history->id,
                    $contact->id, $contact->mobile, $message
                )->onQueue('whatsapp');
            }
        }
    }
}
