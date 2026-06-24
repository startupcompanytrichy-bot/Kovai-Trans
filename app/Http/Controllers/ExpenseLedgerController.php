<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseLedgerController extends Controller
{
    public function index(Request $request)
    {
        $categories = $this->getCategories();

        $query = Expense::where('is_deleted', false);

        \applyFinYearFilter($query);

        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        $summary = (clone $query)
            ->selectRaw('category, count(*) as count, sum(amount) as total')
            ->groupBy('category')
            ->get()
            ->keyBy('category')
            ->map(function ($row) {
                return [
                    'count' => $row->count,
                    'total' => $row->total,
                ];
            })
            ->toArray();

        $todayCountByCategory = Expense::where('is_deleted', false)
            ->whereDate('expense_date', now()->format('Y-m-d'))
            ->selectRaw('category, count(*) as today_count')
            ->groupBy('category')
            ->pluck('today_count', 'category')
            ->toArray();

        $todayCount = array_sum($todayCountByCategory);

        return view('Expenses.Ledger_Index', compact('categories', 'summary', 'todayCountByCategory', 'todayCount'));
    }

    public function showCategory(Request $request, string $category)
    {
        $categories = $this->getCategories();

        if (!isset($categories[$category])) {
            abort(404);
        }

        $query = Expense::with(['trip', 'vehicle', 'driver'])
            ->where('is_deleted', false)
            ->where('category', $category);

        \applyFinYearFilter($query);

        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->get();

        $summary = [
            'count' => $expenses->count(),
            'total' => $expenses->sum('amount'),
        ];

        return view('Expenses.Ledger_List', compact('category', 'categories', 'expenses', 'summary'));
    }

    public function pdf(Request $request, string $category)
    {
        $categories = $this->getCategories();

        if (!isset($categories[$category])) {
            abort(404);
        }

        $query = Expense::with(['trip', 'vehicle', 'driver'])
            ->where('is_deleted', false)
            ->where('category', $category);

        \applyFinYearFilter($query);

        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->get();

        $summary = [
            'count' => $expenses->count(),
            'total' => $expenses->sum('amount'),
        ];

        $catLabel = $categories[$category]['label'] ?? ucfirst($category);
        $dateFrom = $request->date_from;
        $dateTo   = $request->date_to;

        return view('Expenses.Ledger_Print', compact('category', 'categories', 'expenses', 'summary', 'catLabel', 'dateFrom', 'dateTo'));
    }

    private function getCategories(): array
    {
        try {
            $map = ExpenseCategory::allAsMap();
            return !empty($map) ? $map : Expense::$categories;
        } catch (\Exception $e) {
            return Expense::$categories;
        }
    }
}
