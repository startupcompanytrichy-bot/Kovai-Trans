<?php

namespace App\Jobs;

use App\Models\Setting;
use App\Models\VehicleEmi;
use App\Models\WhatsAppReminderContact;
use App\Models\WhatsAppHistory;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * SendEmiReminderJob  (Prep / Orchestrator Job)
 *
 * Triggered daily at 9:00 AM IST by the scheduler.
 *
 * For every active VehicleEmi:
 *   1. Reads "Days Before Due" window from Settings (whatsapp_reminder_days).
 *   2. Sends if next_due_date is within that window (or overdue).
 *   3. Skips if already sent today for this EMI.
 *   4. Builds a rich EMI message with all financial details.
 *   5. Creates pending whatsapp_histories record.
 *   6. Dispatches ONE SendSingleWhatsAppJob per contact → "whatsapp" queue.
 *
 * Does NOT call WhatsApp directly.
 */
class SendEmiReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 1;
    public int $timeout = 120;

    public function handle(): void
    {
        $reminderDays = (int) Setting::getValue('whatsapp_reminder_days', 15);

        Log::info("[SendEmiReminderJob] Started — reminder window: {$reminderDays} days.");

        $contacts = WhatsAppReminderContact::where('is_active', true)->get();

        if ($contacts->isEmpty()) {
            Log::warning('[SendEmiReminderJob] No active contacts — aborting.');
            return;
        }

        $today      = Carbon::now()->startOfDay();
        $appName    = config('app.name', 'Trans ERP');
        $emis       = VehicleEmi::with('vehicle')
                        ->where('status', 'active')
                        ->where('is_deleted', false)
                        ->whereNotNull('next_due_date')
                        ->get();

        $dispatched = 0;
        $skipped    = 0;

        foreach ($emis as $emi) {
            $dueDate       = Carbon::parse($emi->next_due_date)->startOfDay();
            $daysRemaining = (int) $today->diffInDays($dueDate, false);

            // Only send within the configured window (or overdue)
            if ($daysRemaining > $reminderDays) continue;

            // Skip if already sent today for this EMI
            $alreadySentToday = WhatsAppHistory::where('source', 'emi_scheduled')
                ->where('vehicle_id', $emi->vehicle_id)
                ->whereDate('created_at', $today->toDateString())
                ->where('send_status', 'sent')
                ->exists();

            if ($alreadySentToday) {
                $skipped++;
                Log::info("[SendEmiReminderJob] Skip (dup): EMI #{$emi->id} — " . ($emi->vehicle->vehicle_number ?? 'N/A'));
                continue;
            }

            $message = $this->buildEmiMessage($emi, $daysRemaining, $appName);

            foreach ($contacts as $contact) {
                $history = WhatsAppHistory::create([
                    'company_id'     => $emi->company_id,
                    'branch_id'      => $emi->branch_id,
                    'source'         => 'emi_scheduled',
                    'vehicle_id'     => $emi->vehicle_id,
                    'contact_id'     => $contact->id,
                    'document_type'  => 'emi_due',
                    'document_label' => 'EMI Due',
                    'expiry_date'    => $dueDate->format('Y-m-d'),
                    'days_remaining' => $daysRemaining,
                    'contact_number' => $contact->mobile,
                    'message'        => $message,
                    'send_status'    => 'pending',
                    'created_by'     => null,
                ]);

                SendSingleWhatsAppJob::dispatch(
                    0,             // no vehicle_reminder_sends record for EMI
                    $history->id,
                    $contact->id,
                    $contact->mobile,
                    $message
                )->onQueue('whatsapp');

                $dispatched++;
                Log::info("[SendEmiReminderJob] Queued: EMI #{$emi->id} → {$contact->mobile}");
            }
        }

        Log::info("[SendEmiReminderJob] Done — Dispatched: {$dispatched} | Skipped: {$skipped}");
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('[SendEmiReminderJob] Failed: ' . $exception->getMessage());
    }

    // ── Message Builder ───────────────────────────────────────────────────────

    protected function buildEmiMessage(VehicleEmi $emi, int $daysRemaining, string $appName): string
    {
        $vehicle       = $emi->vehicle;
        $vehicleNo     = $vehicle->vehicle_number ?? 'N/A';
        $dueDateStr    = Carbon::parse($emi->next_due_date)->format('d M Y');
        $separator     = str_repeat('─', 30);

        // Financial figures
        $emiAmount       = '₹' . number_format((float) $emi->emi_amount,       2);
        $totalPayable    = '₹' . number_format((float) $emi->total_payable,    2);
        $outstandingBal  = '₹' . number_format((float) $emi->outstanding_balance, 2);
        $paidEmis        = (int) ($emi->paid_emis  ?? 0);
        $totalEmis       = (int) ($emi->total_emis ?? 0);
        $pendingEmis     = max(0, $totalEmis - $paidEmis);

        // Header based on urgency
        if ($daysRemaining < 0) {
            $overdue = abs($daysRemaining);
            $header  = "🚨 *EMI Payment Overdue — Action Required*";
            $urgency = "❗ Payment was due *{$overdue} day" . ($overdue > 1 ? 's' : '') . " ago*.\nPlease pay *immediately* to avoid penalties.";
        } elseif ($daysRemaining === 0) {
            $header  = "⚠️ *EMI Payment Due TODAY*";
            $urgency = "❗ Your EMI payment is due *today*.\nPlease make the payment immediately.";
        } elseif ($daysRemaining <= 3) {
            $header  = "🔴 *EMI Payment Due in {$daysRemaining} Day" . ($daysRemaining > 1 ? 's' : '') . "*";
            $urgency = "❗ *Urgent* — Only {$daysRemaining} day" . ($daysRemaining > 1 ? 's' : '') . " left to pay.";
        } elseif ($daysRemaining <= 7) {
            $header  = "🟠 *EMI Payment Reminder — {$daysRemaining} Days Left*";
            $urgency = "Please arrange payment before the due date.";
        } else {
            $header  = "📋 *EMI Payment Reminder*";
            $urgency = "Please ensure timely payment before the due date.";
        }

        return implode("\n", [
            $header,
            "",
            $separator,
            "🏢 *{$appName}*",
            $separator,
            "",
            "🚛 *Vehicle No:*     {$vehicleNo}",
            "🏦 *Financier:*      " . ($emi->financier_name ?? 'N/A'),
            "📅 *Due Date:*       {$dueDateStr}",
            "⏰ *Days Remaining:* " . ($daysRemaining < 0 ? abs($daysRemaining) . " days overdue" : ($daysRemaining === 0 ? "Due today" : "{$daysRemaining} days")),
            "",
            $separator,
            "💰 *Payment Details*",
            $separator,
            "💵 *EMI Amount:*       {$emiAmount}",
            "📊 *Total Amount:*     {$totalPayable}",
            "✅ *EMIs Paid:*        {$paidEmis} / {$totalEmis}",
            "🔄 *Dues Remaining:*   {$pendingEmis} instalment" . ($pendingEmis !== 1 ? 's' : ''),
            "📉 *Balance Amount:*   {$outstandingBal}",
            "",
            $urgency,
            "",
            $separator,
            "_Sent by {$appName} Vehicle Management_",
        ]);
    }
}
