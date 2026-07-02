<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\FinancialYear;
use App\Models\Payroll;
use App\Models\SalaryAdvance;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayrollController extends Controller
{
    /* ─── helpers ─── */
    private function currentSession(): array
    {
        $user = Auth::user() ?? session('user');
        return [
            'company_id' => $user->company_id ?? session('company_id'),
            'branch_id'  => $user->branch_id  ?? session('branch_id'),
            'fin_year'   => session('fin_year'),
            'user_id'    => $user->id          ?? null,
        ];
    }

    private function drivers()
    {
        $s = $this->currentSession();
        return Driver::where('is_active', true)
            ->where('is_deleted', false)
            ->when($s['company_id'], fn($q) => $q->where('company_id', $s['company_id']))
            ->when($s['branch_id'],  fn($q) => $q->where('branch_id',  $s['branch_id']))
            ->orderBy('name')
            ->get();
    }

    /* ─────────────────────────────────── INDEX ─── */
    public function index(Request $request)
    {
        $s = $this->currentSession();

        $query = Payroll::active()
            ->when($s['company_id'], fn($q) => $q->where('company_id', $s['company_id']))
            ->when($s['branch_id'],  fn($q) => $q->where('branch_id',  $s['branch_id']));

        // filters
        if ($request->filled('month')) {
            $query->whereYear('payroll_month',  substr($request->month, 0, 4))
                  ->whereMonth('payroll_month', substr($request->month, 5, 2));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('employee')) {
            $query->where('employee_name', 'like', '%'.$request->employee.'%');
        }

        $payrolls = $query->orderByDesc('payroll_month')->orderBy('employee_name')->get();

        // stats
        $stats = [
            'total'   => $payrolls->count(),
            'paid'    => $payrolls->where('status', 'paid')->count(),
            'pending' => $payrolls->where('status', 'pending')->count(),
            'total_net' => $payrolls->sum('net_salary'),
        ];

        // advances
        $advances = SalaryAdvance::where('is_deleted', false)
            ->when($s['company_id'], fn($q) => $q->where('company_id', $s['company_id']))
            ->when($s['branch_id'],  fn($q) => $q->where('branch_id',  $s['branch_id']))
            ->orderByDesc('advance_date')
            ->get();

        $drivers = $this->drivers();

        // trips for advance modal (recent 200, with driver info)
        $trips = Trip::where('is_deleted', false)
            ->when($s['company_id'], fn($q) => $q->where('company_id', $s['company_id']))
            ->when($s['branch_id'],  fn($q) => $q->where('branch_id',  $s['branch_id']))
            ->with('driver')
            ->orderByDesc('trip_date')
            ->limit(200)
            ->get(['id','trip_no','trip_date','from_location','to_location','driver_id']);

        return view('Payroll.index', compact('payrolls', 'stats', 'advances', 'drivers', 'trips'));
    }

    /* ─────────────────────────────────── CREATE ─── */
    public function create()
    {
        $drivers = $this->drivers();

        // outstanding advances per driver
        $advanceBalances = SalaryAdvance::where('is_deleted', false)
            ->whereIn('status', ['pending', 'partial'])
            ->selectRaw('driver_id, SUM(amount - recovered_amount) as balance')
            ->groupBy('driver_id')
            ->pluck('balance', 'driver_id');

        return view('Payroll.add', compact('drivers', 'advanceBalances'));
    }

    /* ─────────────────────────────────── STORE ─── */
    public function store(Request $request)
    {
        $request->validate([
            'employee_name'   => 'required|string|max:255',
            'payroll_month'   => 'required|date',
            'basic_salary'    => 'required|numeric|min:0',
            'hra'             => 'nullable|numeric|min:0',
            'da'              => 'nullable|numeric|min:0',
            'other_allowance' => 'nullable|numeric|min:0',
            'bonus'           => 'nullable|numeric|min:0',
            'pf'              => 'nullable|numeric|min:0',
            'esi'             => 'nullable|numeric|min:0',
            'tds'             => 'nullable|numeric|min:0',
            'advance_deduction' => 'nullable|numeric|min:0',
            'other_deduction' => 'nullable|numeric|min:0',
            'payment_mode'    => 'nullable|string|max:20',
            'payment_date'    => 'nullable|date',
        ]);

        $s = $this->currentSession();

        $gross = array_sum([
            $request->basic_salary,
            $request->hra ?? 0,
            $request->da  ?? 0,
            $request->other_allowance ?? 0,
            $request->bonus ?? 0,
        ]);
        $deductions = array_sum([
            $request->pf  ?? 0,
            $request->esi ?? 0,
            $request->tds ?? 0,
            $request->advance_deduction ?? 0,
            $request->other_deduction   ?? 0,
        ]);
        $net = max(0, $gross - $deductions);

        $payroll = Payroll::create([
            'company_id'       => $s['company_id'],
            'branch_id'        => $s['branch_id'],
            'fin_year'         => $s['fin_year'],
            'driver_id'        => $request->driver_id ?: null,
            'employee_name'    => $request->employee_name,
            'employee_type'    => $request->driver_id ? 'driver' : 'staff',
            'payroll_month'    => date('Y-m-01', strtotime($request->payroll_month)),
            'basic_salary'     => $request->basic_salary,
            'hra'              => $request->hra ?? 0,
            'da'               => $request->da  ?? 0,
            'other_allowance'  => $request->other_allowance ?? 0,
            'bonus'            => $request->bonus ?? 0,
            'pf'               => $request->pf  ?? 0,
            'esi'              => $request->esi ?? 0,
            'tds'              => $request->tds ?? 0,
            'advance_deduction'=> $request->advance_deduction ?? 0,
            'other_deduction'  => $request->other_deduction  ?? 0,
            'gross_salary'     => $gross,
            'total_deductions' => $deductions,
            'net_salary'       => $net,
            'payment_mode'     => $request->payment_mode ?? 'cash',
            'reference_no'     => $request->reference_no,
            'payment_date'     => $request->payment_date ?: null,
            'status'           => $request->payment_date ? 'paid' : 'pending',
            'notes'            => $request->notes,
            'created_by'       => $s['user_id'],
        ]);

        // If advance was deducted, update the advance recovery
        if ($request->filled('driver_id') && ($request->advance_deduction ?? 0) > 0) {
            $advances = SalaryAdvance::where('driver_id', $request->driver_id)
                ->whereIn('status', ['pending', 'partial'])
                ->where('is_deleted', false)
                ->orderBy('advance_date')
                ->get();

            $toRecover = (float) $request->advance_deduction;
            foreach ($advances as $adv) {
                if ($toRecover <= 0) break;
                $pending = (float)$adv->amount - (float)$adv->recovered_amount;
                $recover = min($pending, $toRecover);
                $adv->recovered_amount += $recover;
                $adv->status = $adv->recovered_amount >= $adv->amount ? 'recovered' : 'partial';
                $adv->save();
                $toRecover -= $recover;
            }
        }

        return redirect()->route('payroll')->with('success', 'Payroll record saved successfully.');
    }

    /* ─────────────────────────────────── VIEW ─── */
    public function view($id)
    {
        $payroll = Payroll::active()->findOrFail($id);
        return view('Payroll.view', compact('payroll'));
    }

    /* ─────────────────────────────────── EDIT ─── */
    public function edit($id)
    {
        $payroll  = Payroll::active()->findOrFail($id);
        $drivers  = $this->drivers();

        $advanceBalances = SalaryAdvance::where('is_deleted', false)
            ->whereIn('status', ['pending', 'partial'])
            ->selectRaw('driver_id, SUM(amount - recovered_amount) as balance')
            ->groupBy('driver_id')
            ->pluck('balance', 'driver_id');

        return view('Payroll.edit', compact('payroll', 'drivers', 'advanceBalances'));
    }

    /* ─────────────────────────────────── UPDATE ─── */
    public function update(Request $request, $id)
    {
        $payroll = Payroll::active()->findOrFail($id);

        $request->validate([
            'employee_name'   => 'required|string|max:255',
            'payroll_month'   => 'required|date',
            'basic_salary'    => 'required|numeric|min:0',
            'hra'             => 'nullable|numeric|min:0',
            'da'              => 'nullable|numeric|min:0',
            'other_allowance' => 'nullable|numeric|min:0',
            'bonus'           => 'nullable|numeric|min:0',
            'pf'              => 'nullable|numeric|min:0',
            'esi'             => 'nullable|numeric|min:0',
            'tds'             => 'nullable|numeric|min:0',
            'advance_deduction' => 'nullable|numeric|min:0',
            'other_deduction' => 'nullable|numeric|min:0',
            'payment_mode'    => 'nullable|string|max:20',
            'payment_date'    => 'nullable|date',
        ]);

        $s = $this->currentSession();

        $gross = array_sum([
            $request->basic_salary,
            $request->hra ?? 0,
            $request->da  ?? 0,
            $request->other_allowance ?? 0,
            $request->bonus ?? 0,
        ]);
        $deductions = array_sum([
            $request->pf  ?? 0,
            $request->esi ?? 0,
            $request->tds ?? 0,
            $request->advance_deduction ?? 0,
            $request->other_deduction   ?? 0,
        ]);
        $net = max(0, $gross - $deductions);

        $payroll->update([
            'driver_id'        => $request->driver_id ?: null,
            'employee_name'    => $request->employee_name,
            'employee_type'    => $request->driver_id ? 'driver' : 'staff',
            'payroll_month'    => date('Y-m-01', strtotime($request->payroll_month)),
            'basic_salary'     => $request->basic_salary,
            'hra'              => $request->hra ?? 0,
            'da'               => $request->da  ?? 0,
            'other_allowance'  => $request->other_allowance ?? 0,
            'bonus'            => $request->bonus ?? 0,
            'pf'               => $request->pf  ?? 0,
            'esi'              => $request->esi ?? 0,
            'tds'              => $request->tds ?? 0,
            'advance_deduction'=> $request->advance_deduction ?? 0,
            'other_deduction'  => $request->other_deduction  ?? 0,
            'gross_salary'     => $gross,
            'total_deductions' => $deductions,
            'net_salary'       => $net,
            'payment_mode'     => $request->payment_mode ?? 'cash',
            'reference_no'     => $request->reference_no,
            'payment_date'     => $request->payment_date ?: null,
            'status'           => $request->payment_date ? 'paid' : 'pending',
            'notes'            => $request->notes,
            'updated_by'       => $s['user_id'],
        ]);

        return redirect()->route('payroll')->with('success', 'Payroll record updated successfully.');
    }

    /* ─────────────────────────────────── DESTROY ─── */
    public function destroy($id)
    {
        $payroll = Payroll::active()->findOrFail($id);
        $payroll->update(['is_deleted' => true]);
        return redirect()->route('payroll')->with('success', 'Payroll record deleted.');
    }

    /* ─────────────────────────── ADVANCE: STORE ─── */
    public function advanceStore(Request $request)
    {
        $request->validate([
            'employee_name' => 'required|string|max:255',
            'amount'        => 'required|numeric|min:1',
            'advance_date'  => 'required|date',
            'payment_mode'  => 'nullable|string|max:20',
        ]);

        $s = $this->currentSession();

        SalaryAdvance::create([
            'company_id'   => $s['company_id'],
            'branch_id'    => $s['branch_id'],
            'fin_year'     => $s['fin_year'],
            'driver_id'    => $request->driver_id ?: null,
            'trip_id'      => $request->trip_id   ?: null,
            'employee_name'=> $request->employee_name,
            'amount'       => $request->amount,
            'advance_date' => $request->advance_date,
            'payment_mode' => $request->payment_mode ?? 'cash',
            'reference_no' => $request->reference_no,
            'notes'        => $request->notes,
            'created_by'   => $s['user_id'],
        ]);

        return redirect()->route('payroll')->with('success', 'Salary advance recorded.');
    }

    /* ─────────────────────────── ADVANCE: EDIT ─── */
    public function advanceEdit($id)
    {
        $advance = SalaryAdvance::where('is_deleted', false)->findOrFail($id);
        $drivers = $this->drivers();
        $trips = Trip::where('is_deleted', false)
            ->whereHas('driver')
            ->with('driver')
            ->orderByDesc('trip_date')
            ->limit(200)
            ->get(['id','trip_no','trip_date','from_location','to_location','driver_id']);

        return view('Payroll.advance_edit', compact('advance', 'drivers', 'trips'));
    }

    /* ─────────────────────────── ADVANCE: UPDATE ─── */
    public function advanceUpdate(Request $request, $id)
    {
        $advance = SalaryAdvance::where('is_deleted', false)->findOrFail($id);

        $request->validate([
            'employee_name' => 'required|string|max:255',
            'amount'        => 'required|numeric|min:1',
            'advance_date'  => 'required|date',
            'payment_mode'  => 'nullable|string|max:20',
        ]);

        $advance->update([
            'driver_id'    => $request->driver_id ?: null,
            'trip_id'      => $request->trip_id   ?: null,
            'employee_name'=> $request->employee_name,
            'amount'       => $request->amount,
            'advance_date' => $request->advance_date,
            'payment_mode' => $request->payment_mode ?? 'cash',
            'reference_no' => $request->reference_no,
            'notes'        => $request->notes,
        ]);

        return redirect()->route('payroll', ['tab' => 'advances'])->with('success', 'Advance record updated.');
    }

    /* ─────────────────────────── ADVANCE: DELETE ─── */
    public function advanceDestroy($id)
    {
        $advance = SalaryAdvance::where('is_deleted', false)->findOrFail($id);
        $advance->update(['is_deleted' => true]);
        return redirect()->route('payroll')->with('success', 'Advance record deleted.');
    }

    /* ────────────────── API: driver advances list ─── */
    public function driverAdvanceBalance($driverId)
    {
        $advances = SalaryAdvance::where('driver_id', $driverId)
            ->whereIn('status', ['pending', 'partial'])
            ->where('is_deleted', false)
            ->orderBy('advance_date')
            ->get(['id', 'advance_date', 'amount', 'recovered_amount']);

        $items = $advances->map(function ($a) {
            return [
                'id'              => $a->id,
                'date'            => $a->advance_date->format('d M Y'),
                'amount'          => (float) $a->amount,
                'recovered'       => (float) $a->recovered_amount,
                'pending'         => (float) $a->amount - (float) $a->recovered_amount,
            ];
        });

        return response()->json([
            'balance' => round($items->sum('pending'), 2),
            'items'   => $items,
        ]);
    }
}
