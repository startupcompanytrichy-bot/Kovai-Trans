<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ExpensePayment;
use App\Models\Trader;
use App\Models\ExpenseAccessory;
use App\Models\Trip;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['trip', 'vehicle', 'driver'])
            ->where('is_deleted', false)
            ->orderBy('expense_date', 'desc');

        // Apply financial year filter first (unless overridden by manual date filters below)
        if (!$request->filled('date_from') && !$request->filled('date_to')) {
            \applyFinYearFilter($query);
        }

        if ($request->filled('category')) $query->where('category', $request->category);
        if ($request->filled('status'))   $query->where('status', $request->status);
        if ($request->filled('date_from')) $query->whereDate('expense_date', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('expense_date', '<=', $request->date_to);

        $expenses = $query->get();

        $stats = [
            'total'       => $expenses->sum('amount'),
            'pending'     => $expenses->where('status', 'pending')->sum('amount'),
            'approved'    => $expenses->where('status', 'approved')->sum('amount'),
            'count'       => $expenses->count(),
            'by_category' => $expenses->groupBy('category')->map(fn($g) => $g->sum('amount')),
        ];

        $categories = $this->getCategories();

        return view('Expenses.Expense_Table', compact('expenses', 'stats', 'categories'));
    }

    public function create()
    {
        return view('Expenses.New_Expense', $this->formData());
    }

    public function store(Request $request)
    {
        $validated = $this->validateExpense($request);

        if ($request->hasFile('bill_image')) {
            $validated['bill_image'] = $request->file('bill_image')
                ->store('expenses/bills', 'public');
        }

        if (auth()->check()) {
            $validated['created_by'] = auth()->id();
        }

        $validated['fin_year'] = \currentFY()?->id;
        Expense::create($validated);

        return redirect()->route('expense')->with('success', 'Expense added successfully');
    }

    /**
     * Store a new custom expense category (AJAX)
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:50',
            'icon'  => 'required|string|max:50',
            'color' => 'required|string|max:20',
            'bg'    => 'required|string|max:20',
        ]);

        $key = Str::slug($request->label, '_');

        // Ensure unique key
        $base = $key;
        $i = 1;
        while (ExpenseCategory::where('key', $key)->exists()) {
            $key = $base . '_' . $i++;
        }

        $cat = ExpenseCategory::create([
            'key'        => $key,
            'label'      => $request->label,
            'icon'       => $request->icon,
            'color'      => $request->color,
            'bg'         => $request->bg,
            'is_custom'  => true,
            'is_active'  => true,
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'key'     => $cat->key,
            'label'   => $cat->label,
            'icon'    => $cat->icon,
            'color'   => $cat->color,
            'bg'      => $cat->bg,
        ]);
    }

    public function edit($id)
    {
        $expense = Expense::with('accessories')->findOrFail($id);
        return view('Expenses.Edit_Expense', array_merge($this->formData(), compact('expense')));
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        $validated = $this->validateExpense($request, $id);

        if ($request->hasFile('bill_image')) {
            if ($expense->bill_image) Storage::disk('public')->delete($expense->bill_image);
            $validated['bill_image'] = $request->file('bill_image')->store('expenses/bills', 'public');
        }

        if (auth()->check()) $validated['updated_by'] = auth()->id();

        $expense->update($validated);

        return redirect()->route('expense')->with('success', 'Expense updated successfully');
    }

    public function approve($id)
    {
        Expense::findOrFail($id)->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        return redirect()->back()->with('success', 'Expense approved');
    }

    public function reject($id)
    {
        Expense::findOrFail($id)->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Expense rejected');
    }

    public function destroy($id)
    {
        Expense::findOrFail($id)->update(['is_deleted' => true]);
        return redirect()->route('expense')->with('success', 'Expense deleted');
    }

    // ── Payment Collection (Credit Expenses) ─────────────────────────────────

    /**
     * Record a payment against a credit expense (partial or full).
     */
    public function collectPayment(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        if ($expense->payment_mode !== 'credit') {
            return response()->json(['error' => 'Only credit expenses can collect payments.'], 422);
        }

        if ($expense->status !== 'approved') {
            return response()->json(['error' => 'Payment can only be collected after the expense is approved.'], 422);
        }

        $balance = max(0, (float) $expense->amount - (float) $expense->paid_amount);

        $request->validate([
            'payment_date' => 'required|date',
            'amount'       => 'required|numeric|min:0.01|max:' . $balance,
            'payment_mode' => 'required|string|in:cash,upi,bank,cheque',
            'reference_no' => 'nullable|string|max:100',
            'notes'        => 'nullable|string|max:500',
        ]);

        // Record individual payment
        ExpensePayment::create([
            'fin_year'     => \currentFY()?->id,
            'expense_id'   => $expense->id,
            'payment_date' => $request->payment_date,
            'amount'       => $request->amount,
            'payment_mode' => $request->payment_mode,
            'reference_no' => $request->reference_no,
            'notes'        => $request->notes,
            'created_by'   => auth()->id(),
        ]);

        // Recompute totals from all payments
        $totalPaid     = $expense->payments()->sum('amount');
        $newBalance    = max(0, (float) $expense->amount - $totalPaid);
        $paymentStatus = $newBalance <= 0 ? 'paid' : ($totalPaid > 0 ? 'partial' : 'unpaid');

        $expense->update([
            'paid_amount'    => $totalPaid,
            'payment_status' => $paymentStatus,
        ]);

        return response()->json([
            'success'        => true,
            'paid_amount'    => number_format($totalPaid, 2),
            'balance'        => number_format($newBalance, 2),
            'payment_status' => $paymentStatus,
            'message'        => $paymentStatus === 'paid'
                ? 'Payment complete! Expense fully settled.'
                : 'Payment of ₹' . number_format($request->amount, 2) . ' recorded. Balance: ₹' . number_format($newBalance, 2),
        ]);
    }

    /**
     * Return payment history for an expense (JSON for the panel).
     */
    public function paymentHistory($id)
    {
        $expense  = Expense::findOrFail($id);
        $payments = $expense->payments()->with('createdBy')->get()->map(function ($p) {
            return [
                'id'           => $p->id,
                'payment_date' => $p->payment_date->format('d M Y'),
                'amount'       => number_format($p->amount, 2),
                'payment_mode' => $p->payment_mode,
                'reference_no' => $p->reference_no,
                'notes'        => $p->notes,
                'created_by'   => optional($p->createdBy)->name ?? '—',
            ];
        });

        return response()->json([
            'expense_id'     => $expense->id,
            'expense_status' => $expense->status,
            'total_amount'   => number_format($expense->amount, 2),
            'paid_amount'    => number_format($expense->paid_amount, 2),
            'balance'        => number_format(max(0, $expense->amount - $expense->paid_amount), 2),
            'payment_status' => $expense->payment_status,
            'payments'       => $payments,
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function getCategories(): array
    {
        // Try DB first, fall back to static array
        try {
            $map = ExpenseCategory::allAsMap();
            return !empty($map) ? $map : Expense::$categories;
        } catch (\Exception $e) {
            return Expense::$categories;
        }
    }

    private function formData(): array
    {
        // Load all active traders; group them so JS can filter by selected category.
        // A trader with category=null is "global" and appears under every category.
        $allTraders = Trader::where('is_active', true)
            ->where('is_deleted', false);

        \applyFinYearFilter($allTraders);

        $allTraders = $allTraders->orderBy('name')->get(['id', 'name', 'category']);

        // Build a map: ['fuel' => [{id,name}, ...], 'repair' => [...], '_global' => [...]]
        $tradersByCategory = [];
        foreach ($allTraders as $t) {
            $key = $t->category ?? '_global';
            $tradersByCategory[$key][] = ['id' => $t->id, 'name' => $t->name];
        }

        $tripsQuery = Trip::with(['vehicle', 'driver'])
            ->where('is_deleted', false);

        \applyFinYearFilter($tripsQuery);

        return [
            'trips'              => $tripsQuery->orderBy('trip_no')
                                        ->get(['id','trip_no','trip_date','from_location','to_location','vehicle_id','driver_id']),
            'vehicles'           => Vehicle::orderBy('vehicle_number')->get(),
            'drivers'            => Driver::where('is_active', true)->where('is_deleted', false)->orderBy('name')->get(),
            'traders'            => $allTraders,
            'tradersByCategory'  => $tradersByCategory,
            'categories'         => $this->getCategories(),
        ];
    }

    private function validateExpense(Request $request, ?int $id = null): array
    {
        $validKeys = array_keys($this->getCategories());

        $rules = [
            'trip_id'      => 'nullable|exists:trips,id',
            'vehicle_id'   => 'nullable|exists:vehicles,id',
            'driver_id'    => 'nullable|exists:drivers,id',
            'trader_id'    => 'nullable|exists:traders,id',
            'category'     => 'required|string|in:' . implode(',', $validKeys),
            'amount'       => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'payment_mode' => 'nullable|string|in:cash,credit',
            'notes'        => 'nullable|string',
            'bill_image'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'status'       => 'nullable|string|in:pending,approved,rejected',
        ];

        return $request->validate($rules);
    }
}
