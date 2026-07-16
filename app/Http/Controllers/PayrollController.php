<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Expense;
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
            'fin_year'   => \currentFY()?->id,
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

        // Scope to current financial year
        \applyFinYearFilter($query);

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
            'total'     => $payrolls->count(),
            'paid'      => $payrolls->where('status', 'paid')->count(),
            'pending'   => $payrolls->where('status', 'pending')->count(),
            'total_net' => $payrolls->sum('net_salary'),
        ];

        // advances — scoped to current FY
        $advQuery = SalaryAdvance::where('is_deleted', false)
            ->when($s['company_id'], fn($q) => $q->where('company_id', $s['company_id']))
            ->when($s['branch_id'],  fn($q) => $q->where('branch_id',  $s['branch_id']));
        \applyFinYearFilter($advQuery);
        $advances = $advQuery->orderByDesc('advance_date')->get();

        $drivers = $this->drivers();

        // trips for advance modal — scoped to current FY
        $tripsQuery = Trip::where('is_deleted', false)
            ->when($s['company_id'], fn($q) => $q->where('company_id', $s['company_id']))
            ->when($s['branch_id'],  fn($q) => $q->where('branch_id',  $s['branch_id']))
            ->with('driver')
            ->orderByDesc('trip_date')
            ->limit(200);
        \applyFYFilter($tripsQuery, 'trip_date');
        $trips = $tripsQuery->get(['id','trip_no','trip_date','from_location','to_location','driver_id']);

        // Employee-level detail aggregates
        $employeeDetails = [];
        $allEmpNames = $payrolls->pluck('employee_name')
            ->merge($advances->pluck('employee_name'))
            ->unique()->filter()->values();

        foreach ($allEmpNames as $name) {
            $empAdvances = $advances->where('employee_name', $name);
            $empPayrolls = $payrolls->where('employee_name', $name);
            $employeeDetails[$name] = [
                'trip_advances'     => $empAdvances->whereNotNull('trip_id')->sum('amount'),
                'normal_advances'   => $empAdvances->whereNull('trip_id')->sum('amount'),
                'advance_collected' => $empAdvances->sum('recovered_amount'),
                'balance_amount'    => $empAdvances->sum(fn($a) => (float)$a->amount - (float)$a->recovered_amount),
                'paid_amount'       => $empPayrolls->where('status', 'paid')->sum('net_salary'),
            ];
        }

        $currentFY = \currentFY();

        return view('Payroll.index', compact('payrolls', 'stats', 'advances', 'drivers', 'trips', 'employeeDetails', 'currentFY'));
    }

    /* ─────────────────────────────────── CREATE ─── */
    public function create(Request $request)
    {
        $s = $this->currentSession();
        $drivers = $this->drivers();
        $currentFY = \currentFY();

        $presetEmployee = $request->input('employee');

        // Outstanding advances per driver — all FY (pending balance is cumulative)
        $advanceBalances = SalaryAdvance::where('is_deleted', false)
            ->whereIn('status', ['pending', 'partial'])
            ->when($s['company_id'], fn($q) => $q->where('company_id', $s['company_id']))
            ->when($s['branch_id'],  fn($q) => $q->where('branch_id',  $s['branch_id']))
            ->selectRaw('driver_id, SUM(amount - recovered_amount) as balance')
            ->groupBy('driver_id')
            ->pluck('balance', 'driver_id');

        // All advances (all FY) — outstanding balance is cross-FY
        $allAdvances = SalaryAdvance::where('is_deleted', false)
            ->when($s['company_id'], fn($q) => $q->where('company_id', $s['company_id']))
            ->when($s['branch_id'],  fn($q) => $q->where('branch_id',  $s['branch_id']))
            ->get();

        // Payrolls scoped to current FY for the summary stats
        $fyPayrollQuery = Payroll::active()
            ->when($s['company_id'], fn($q) => $q->where('company_id', $s['company_id']))
            ->when($s['branch_id'],  fn($q) => $q->where('branch_id',  $s['branch_id']));
        \applyFinYearFilter($fyPayrollQuery);
        $allPayrolls = $fyPayrollQuery->get();

        $employeeDetails = [];
        $allEmpNames = $allAdvances->pluck('employee_name')
            ->merge($allPayrolls->pluck('employee_name'))
            ->unique()->filter()->values();

        foreach ($allEmpNames as $name) {
            $empAdvances = $allAdvances->where('employee_name', $name);
            $empPayrolls = $allPayrolls->where('employee_name', $name);
            $employeeDetails[$name] = [
                'trip_advances'     => $empAdvances->whereNotNull('trip_id')->sum('amount'),
                'normal_advances'   => $empAdvances->whereNull('trip_id')->sum('amount'),
                'advance_collected' => $empAdvances->sum('recovered_amount'),
                'balance_amount'    => $empAdvances->sum(fn($a) => (float)$a->amount - (float)$a->recovered_amount),
                'paid_amount'       => $empPayrolls->where('status', 'paid')->sum('net_salary'),
            ];
        }

        // Expenses linked to advances
        $advExpenseMap = Expense::whereIn('advance_id', $allAdvances->pluck('id'))
            ->where('is_deleted', false)
            ->get(['advance_id', 'category', 'amount', 'notes'])
            ->groupBy('advance_id');

        $employeeAdvData = $allAdvances->load('trip:id,trip_no,trip_date,from_location,to_location')
            ->groupBy('employee_name')
            ->map(function ($advances) use ($advExpenseMap) {
                return $advances->map(function ($adv) use ($advExpenseMap) {
                    $adv->related_expenses = isset($advExpenseMap[$adv->id])
                        ? $advExpenseMap[$adv->id]->toArray() : [];
                    return $adv;
                });
            });

        // Expenses per employee — scoped to current FY date range
        $expQuery = Expense::where('is_deleted', false)
            ->with('driver:id,name', 'salaryAdvance:id,employee_name')
            ->orderByDesc('expense_date')
            ->limit(300);
        \applyFYFilter($expQuery, 'expense_date');
        $recentExpenses = $expQuery->get();

        $employeeExpenses = [];
        foreach ($recentExpenses as $exp) {
            $emp = $exp->driver?->name ?? $exp->salaryAdvance?->employee_name;
            if (!$emp) continue;
            $employeeExpenses[$emp][] = [
                'date'     => $exp->expense_date,
                'category' => $exp->category,
                'amount'   => $exp->amount,
                'notes'    => $exp->notes,
            ];
        }

        // Trips per driver — scoped to current FY
        $tripQuery = Trip::where('is_deleted', false)
            ->whereNotNull('driver_id')
            ->when($s['company_id'], fn($q) => $q->where('company_id', $s['company_id']))
            ->when($s['branch_id'],  fn($q) => $q->where('branch_id',  $s['branch_id']))
            ->orderByDesc('trip_date');
        \applyFYFilter($tripQuery, 'trip_date');
        $driverTrips = $tripQuery
            ->get(['id','trip_no','trip_date','from_location','to_location','driver_id','driver_bata','freight_amount','advance_amount','status'])
            ->groupBy('driver_id');

        return view('Payroll.add', compact(
            'drivers', 'advanceBalances', 'presetEmployee',
            'employeeDetails', 'driverTrips', 'employeeAdvData',
            'employeeExpenses', 'currentFY'
        ));
    }

    /* ─────────────────────────────────── STORE ─── */
    public function store(Request $request)
    {
        $request->validate([
            'employee_name'    => 'required|string|max:255',
            'payroll_month'    => 'required|date',
            'basic_salary'     => 'required|numeric|min:0',
            'advance_deduction'=> 'nullable|numeric|min:0',
            'advance_ids'      => 'nullable|array',
            'advance_ids.*'    => 'integer|exists:salary_advances,id',
            'payment_mode'     => 'nullable|string|max:20',
            'payment_date'     => 'nullable|date',
        ]);

        $s = $this->currentSession();

        $basic = (float) $request->basic_salary;
        $adv   = (float) ($request->advance_deduction ?? 0);
        $net   = max(0, $basic - $adv);

        $payroll = Payroll::create([
            'company_id'       => $s['company_id'],
            'branch_id'        => $s['branch_id'],
            'fin_year'         => $s['fin_year'],
            'driver_id'        => $request->driver_id ?: null,
            'employee_name'    => $request->employee_name,
            'employee_type'    => $request->driver_id ? 'driver' : 'staff',
            'payroll_month'    => date('Y-m-01', strtotime($request->payroll_month)),
            'basic_salary'     => $basic,
            'advance_deduction'=> $adv,
            'gross_salary'     => $basic,
            'total_deductions' => $adv,
            'net_salary'       => $net,
            'payment_mode'     => $request->payment_mode ?? 'cash',
            'reference_no'     => $request->reference_no,
            'payment_date'     => $request->payment_date ?: null,
            'status'           => $request->payment_date ? 'paid' : 'pending',
            'notes'            => $request->notes,
            'created_by'       => $s['user_id'],
        ]);

        // Recover advances: honour the specific IDs the user checked, fall back to FIFO
        if ($adv > 0) {
            $selectedIds = $request->input('advance_ids', []);

            // Build query — if specific advances were checked use those, else FIFO by date
            $advQuery = SalaryAdvance::where('is_deleted', false)
                ->whereIn('status', ['pending', 'partial']);

            if (!empty($selectedIds)) {
                $advQuery->whereIn('id', $selectedIds);
            } else {
                // Fall back: match by driver or employee name
                $advQuery->where(function ($q) use ($request) {
                    if ($request->filled('driver_id')) {
                        $q->where('driver_id', $request->driver_id);
                    } else {
                        $q->where('employee_name', $request->employee_name);
                    }
                });
            }

            $toRecover = $adv;
            foreach ($advQuery->orderBy('advance_date')->get() as $sa) {
                if ($toRecover <= 0) break;
                $pending = (float)$sa->amount - (float)$sa->recovered_amount;
                $recover = min($pending, $toRecover);
                $sa->recovered_amount += $recover;
                $sa->status = $sa->recovered_amount >= $sa->amount ? 'recovered' : 'partial';
                $sa->save();
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

    /* ─────────────────────────── ADVANCES INDEX ─── */
    public function advancesIndex(Request $request)
    {
        $s = $this->currentSession();
        $currentFY = \currentFY();

        $employeeFilter = $request->input('employee');

        // Advances: outstanding balance is cross-FY — only filter by FY when viewing history
        // When no employee filter: show current FY advances for the list
        // When employee filter: show all their advances regardless of FY (full history for recovery)
        $advQuery = SalaryAdvance::where('is_deleted', false)
            ->when($s['company_id'], fn($q) => $q->where('company_id', $s['company_id']))
            ->when($s['branch_id'],  fn($q) => $q->where('branch_id',  $s['branch_id']))
            ->when($employeeFilter,  fn($q) => $q->where('employee_name', $employeeFilter));

        // Without employee filter — scope to current FY so we don't load all history
        if (!$employeeFilter) {
            \applyFinYearFilter($advQuery);
        }

        $advances = $advQuery->orderByDesc('advance_date')->get();

        $drivers = $this->drivers();

        // Trips for the add-advance modal — scoped to current FY
        $tripsQuery = Trip::where('is_deleted', false)
            ->when($s['company_id'], fn($q) => $q->where('company_id', $s['company_id']))
            ->when($s['branch_id'],  fn($q) => $q->where('branch_id',  $s['branch_id']))
            ->with('driver')
            ->orderByDesc('trip_date')
            ->limit(200);
        \applyFYFilter($tripsQuery, 'trip_date');
        $trips = $tripsQuery->get(['id','trip_no','trip_date','from_location','to_location','driver_id']);

        $grouped = $advances->groupBy('employee_name');
        $categories = Expense::$categories;

        $advanceIds = $advances->pluck('id');
        $recoveries = Expense::whereIn('advance_id', $advanceIds)
            ->where('is_deleted', false)
            ->orderByDesc('expense_date')
            ->get();

        $totalRecovered = $recoveries->sum('amount');

        return view('Payroll.advances_index', compact(
            'advances', 'drivers', 'trips', 'grouped',
            'employeeFilter', 'categories', 'recoveries',
            'totalRecovered', 'currentFY'
        ));
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

    /* ─────────────── ADVANCE RECOVERY: STORE ─── */
    public function advanceRecoveryStore(Request $request)
    {
        $request->validate([
            'employee_name' => 'required|string|max:255',
            'advance_id'    => 'required|exists:salary_advances,id',
            'category'      => 'required|string',
            'amount'        => 'required|numeric|min:0.01',
            'recovery_date' => 'required|date',
            'bill_image'    => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $s = $this->currentSession();

        $advance = SalaryAdvance::where('is_deleted', false)->findOrFail($request->advance_id);

        $pending = (float) $advance->amount - (float) $advance->recovered_amount;
        $toRecover = (float) $request->amount;

        if ($toRecover > $pending) {
            return back()->withErrors(['amount' => 'Recovery amount (₹ '.number_format($toRecover,2).') exceeds pending balance (₹ '.number_format($pending,2).') for this advance.'])->withInput();
        }

        $filePath = null;
        if ($request->hasFile('bill_image')) {
            $filePath = $request->file('bill_image')->store('expenses/bills', 'public');
        }

        // Update the advance
        $advance->recovered_amount += $toRecover;
        $advance->status = $advance->recovered_amount >= $advance->amount ? 'recovered' : 'partial';
        $advance->save();

        // Create expense record
        Expense::create([
            'company_id'    => $s['company_id'],
            'branch_id'     => $s['branch_id'],
            'fin_year'      => $s['fin_year'],
            'advance_id'    => $advance->id,
            'driver_id'     => $advance->driver_id,
            'category'      => $request->category,
            'amount'        => $toRecover,
            'paid_amount'   => $toRecover,
            'payment_status'=> 'paid',
            'expense_date'  => $request->recovery_date,
            'payment_mode'  => 'cash',
            'notes'         => 'Advance recovery — '.$request->employee_name,
            'bill_image'    => $filePath,
            'status'        => 'approved',
            'approved_by'   => $s['user_id'],
            'approved_at'   => now(),
            'created_by'    => $s['user_id'],
        ]);

        return back()->with('success', 'Advance recovery recorded (₹ '.number_format($toRecover,2).'). Balance: ₹ '.number_format($advance->pending_amount,2));
    }

    /* ─── shared: build advance payload ─── */
    private function buildAdvancePayload($advances): array
    {
        // Eager-load trips and expenses in one go
        $advances->load([
            'trip:id,trip_no',
            'expenses' => fn($q) => $q->where('is_deleted', false)->select('advance_id','category','amount','notes','expense_date'),
        ]);

        $items = $advances->map(function ($a) {
            return [
                'id'        => $a->id,
                'date'      => $a->advance_date->format('d M Y'),
                'amount'    => (float) $a->amount,
                'recovered' => (float) $a->recovered_amount,
                'pending'   => (float) $a->amount - (float) $a->recovered_amount,
                'notes'     => $a->notes,
                'trip_id'   => $a->trip_id,
                'trip_no'   => $a->trip?->trip_no,
                'expenses'  => $a->expenses->map(fn($e) => [
                    'category' => $e->category,
                    'amount'   => (float) $e->amount,
                    'notes'    => $e->notes,
                    'date'     => $e->expense_date?->format('d M Y'),
                ])->values(),
            ];
        });

        // All expenses flat list for the expense info box
        $allExpenses = $advances->flatMap(fn($a) => $a->expenses)->map(fn($e) => [
            'category' => $e->category,
            'amount'   => (float) $e->amount,
            'notes'    => $e->notes,
            'date'     => $e->expense_date?->format('d M Y'),
        ])->sortByDesc('date')->values();

        return [
            'balance'   => round($items->sum('pending'), 2),
            'items'     => $items,
            'exp_total' => round($allExpenses->sum('amount'), 2),
            'expenses'  => $allExpenses,
        ];
    }

    /* ────────────────── API: driver advances list ─── */
    public function driverAdvanceBalance($driverId)
    {
        // Pending balance is cross-FY — always show all unpaid advances regardless of year
        $advances = SalaryAdvance::where('driver_id', $driverId)
            ->whereIn('status', ['pending', 'partial'])
            ->where('is_deleted', false)
            ->orderBy('advance_date')
            ->get();

        return response()->json($this->buildAdvancePayload($advances));
    }

    /* ────────────────── API: employee (name) advances list ─── */
    public function employeeAdvanceBalance(Request $request)
    {
        $name = $request->input('name');
        if (!$name) {
            return response()->json(['balance' => 0, 'items' => [], 'exp_total' => 0, 'expenses' => []]);
        }

        // Same — outstanding balance is cross-FY
        $advances = SalaryAdvance::where('employee_name', $name)
            ->whereIn('status', ['pending', 'partial'])
            ->where('is_deleted', false)
            ->orderBy('advance_date')
            ->get();

        return response()->json($this->buildAdvancePayload($advances));
    }
}
