<?php

namespace App\Http\Controllers;

use App\Models\EmiPayment;
use App\Models\Vehicle;
use App\Models\VehicleEmi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VehicleEmiController extends Controller
{
    public function index()
    {
        $emis = VehicleEmi::with(['vehicle', 'payments'])
            ->where('is_deleted', false);

        \applyFinYearFilter($emis);

        $emis = $emis->orderBy('next_due_date')->get();

        $stats = [
            'total_loans'     => $emis->count(),
            'active'          => $emis->where('status', 'active')->count(),
            'overdue'         => $emis->filter(fn($e) => $e->is_overdue)->count(),
            'total_emi'       => $emis->sum('emi_amount'),
            'total_outstanding'=> $emis->sum('outstanding_balance'),
            'upcoming'        => $emis->where('status', 'active')
                ->filter(fn($e) => $e->next_due_date && $e->next_due_date->diffInDays(now()) <= 7)
                ->count(),
        ];

        return view('VehicleEmi.Emi_Table', compact('emis', 'stats'));
    }

    public function create()
{
    $vehicles = Vehicle::where('owner_type', 'Own')
        ->orderBy('vehicle_number')
        ->get();

    return view('VehicleEmi.New_Emi', compact('vehicles'));
}

    public function store(Request $request)
    {
        $validated = $this->validateEmi($request);

        if (auth()->check()) {
            $validated['created_by'] = auth()->id();
        }

        // For existing loans, set paid_emis from what JS computed
        // (it will be overwritten after we count payments anyway)
        $validated['fin_year'] = \currentFY()?->id;
        $emi = VehicleEmi::create($validated);

        // ── Auto-create historical payment records for existing loans ──────────
        // loan_type=existing → insert one EmiPayment per month from first due
        // up to AND INCLUDING the month BEFORE the current month.
        // Current month stays open as next_due_date.
        if ($request->input('loan_type') === 'existing') {

            // Determine first instalment date:
            // Use hidden first_instalment_date if submitted, else start + 1 month
            $startDate = \Carbon\Carbon::parse($validated['loan_start_date']);
            $firstDue  = !empty($validated['first_instalment_date'])
                ? \Carbon\Carbon::parse($validated['first_instalment_date'])->startOfMonth()
                : $startDate->copy()->addMonth()->startOfMonth();

            $today     = \Carbon\Carbon::today();
            $currentYM = (int) $today->format('Ym'); // YYYYMM integer for comparison

            $emiAmount = (float) $emi->emi_amount;
            $userId    = auth()->check() ? auth()->id() : null;
            $payments  = [];
            $paidCount = 0;

            // cursor walks month by month
            $cursor = $firstDue->copy();

            while (true) {
                $cursorYM = (int) $cursor->format('Ym');

                // Stop AT current month — current month is still due, not paid
                if ($cursorYM >= $currentYM) {
                    break;
                }

                // Stop if we have reached total number of EMIs
                if ($emi->total_emis && $paidCount >= (int) $emi->total_emis) {
                    break;
                }

                $payments[] = [
                    'fin_year'       => \currentFY()?->id,
                    'vehicle_emi_id' => $emi->id,
                    'due_month'      => $cursor->copy()->startOfMonth()->toDateString(),
                    'payment_date'   => $cursor->copy()->startOfMonth()->toDateString(),
                    'amount_paid'    => $emiAmount,
                    'penalty'        => 0,
                    'payment_mode'   => null,
                    'reference_no'   => null,
                    'particulars'    => 'Loan Instalment (existing import)',
                    'dr_cr'          => 'CR',
                    'others_amount'  => 0,
                    'notes'          => 'Auto-generated on existing loan import',
                    'created_by'     => $userId,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ];

                $paidCount++;
                $cursor->addMonth();
            }

            if (!empty($payments)) {
                // Bulk insert all historical payments at once
                EmiPayment::insert($payments);

                // Recalculate totals
                $totalPaidAmt = $paidCount * $emiAmount;
                $outstanding  = max(0, (float) $emi->loan_amount - $totalPaidAmt);

                // Next due = 1st of current month
                $nextDue = $today->copy()->startOfMonth();

                $emi->update([
                    'paid_emis'           => $paidCount,
                    'outstanding_balance' => $outstanding,
                    'next_due_date'       => $nextDue->toDateString(),
                    'reminder_sent'       => false,
                    'reminder_sent_at'    => null,
                ]);
            }
        }

        return redirect()->route('emi')->with('success', 'EMI record added successfully');
    }

    public function edit($id)
    {
        $emi      = VehicleEmi::with(['vehicle', 'payments'])->findOrFail($id);
        $vehicles = Vehicle::orderBy('vehicle_number')->get();
        return view('VehicleEmi.Edit_Emi', compact('emi', 'vehicles'));
    }

    public function update(Request $request, $id)
    {
        $emi       = VehicleEmi::findOrFail($id);
        $validated = $this->validateEmi($request, $id);

        if (auth()->check()) {
            $validated['updated_by'] = auth()->id();
        }

        $emi->update($validated);

        return redirect()->route('emi.edit', $id)->with('success', 'EMI record updated');
    }

    public function destroy($id)
    {
        VehicleEmi::findOrFail($id)->update(['is_deleted' => true]);
        return redirect()->route('emi')->with('success', 'EMI record deleted');
    }

    // ── EMI Payment ────────────────────────────────────────────────────────────

    public function payStore(Request $request, $emiId)
    {
        $emi = VehicleEmi::findOrFail($emiId);

        $validated = $request->validate([
            'payment_date'  => 'required|date',
            'amount_paid'   => 'required|numeric|min:0',
            'penalty'       => 'nullable|numeric|min:0',
            'payment_mode'  => 'nullable|string|in:cash,upi,bank,cheque',
            'reference_no'  => 'nullable|string|max:100',
            'receipt_image' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'notes'         => 'nullable|string',
            'particulars'   => 'nullable|string|max:255',
            'dr_cr'         => 'nullable|string|in:DR,CR',
            'others_amount' => 'nullable|numeric|min:0',
        ]);

        if ($request->hasFile('receipt_image')) {
            $validated['receipt_image'] = $request->file('receipt_image')
                ->store('emi/receipts', 'public');
        }

        // Capture the current due month (first day of the due month) before advancing
        $validated['due_month'] = $emi->next_due_date
            ? $emi->next_due_date->startOfMonth()->toDateString()
            : \Carbon\Carbon::parse($validated['payment_date'])->startOfMonth()->toDateString();

        $validated['vehicle_emi_id'] = $emiId;
        if (auth()->check()) {
            $validated['created_by'] = auth()->id();
        }

        $validated['fin_year'] = \currentFY()?->id;
        EmiPayment::create($validated);

        // Update EMI record
        $totalPaid = $emi->payments()->sum('amount_paid');
        
        // Auto-generate next due date (advance by 1 month from payment date)
        $nextDueDate = null;
        if ($emi->next_due_date) {
            $nextDueDate = \Carbon\Carbon::parse($emi->next_due_date)->addMonth();
        } else {
            // If no next_due_date set, use payment_date + 1 month
            $nextDueDate = \Carbon\Carbon::parse($validated['payment_date'])->addMonth();
        }
        
        $emi->update([
            'paid_emis'           => $emi->payments()->count(),
            'outstanding_balance' => max(0, $emi->loan_amount - $totalPaid),
            'next_due_date'       => $nextDueDate, // Auto-advance next payment due date
            'reminder_sent'       => false, // Reset reminder for new due date
            'reminder_sent_at'    => null,
        ]);

        return redirect()->route('emi.edit', $emiId)->with('success', 'Payment recorded successfully');
    }

    private function validateEmi(Request $request, ?int $id = null): array
    {
        $rules = [
            'vehicle_id'            => 'required|exists:vehicles,id',
            'financier_name'        => 'required|string|max:255',
            'contract_no'           => 'nullable|string|max:100',
            'loan_amount'           => 'required|numeric|min:0',
            'interest_amount'       => 'nullable|numeric|min:0',
            'insurance_amount'      => 'nullable|numeric|min:0',
            'total_payable'         => 'nullable|numeric|min:0',
            'emi_amount'            => 'required|numeric|min:0',
            'interest_rate'         => 'nullable|numeric|min:0|max:100',
            'loan_start_date'       => 'required|date',
            'loan_end_date'         => 'nullable|date|after_or_equal:loan_start_date',
            'agreement_date'        => 'nullable|date',
            'first_instalment_date' => 'nullable|date',
            'last_instalment_date'  => 'nullable|date',
            'total_emis'            => 'nullable|integer|min:1',
            'paid_emis'             => 'nullable|integer|min:0',
            'next_due_date'         => 'nullable|date',
            'outstanding_balance'   => 'nullable|numeric|min:0',
            'asset_make'            => 'nullable|string|max:100',
            'asset_type'            => 'nullable|string|max:100',
            'status'                => 'nullable|string|in:active,closed,overdue',
            'notes'                 => 'nullable|string',
        ];

        $validated = $request->validate($rules);

        // Remove loan_type — it's not a DB column, used only for flow control
        unset($validated['loan_type']);

        return $validated;
    }
}
