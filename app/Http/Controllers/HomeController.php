<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Expense;
use App\Models\Party;
use App\Models\Trip;
use App\Models\Vehicle;
use App\Models\VehicleEmi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $today      = Carbon::today();
        $yesterday  = Carbon::yesterday();
        $weekStart  = Carbon::now()->startOfWeek()->toDateString();
        $weekEnd    = Carbon::now()->endOfWeek()->toDateString();
        $monthStart = Carbon::now()->startOfMonth()->toDateString();
        $monthEnd   = Carbon::now()->endOfMonth()->toDateString();
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth()->toDateString();
        $lastMonthEnd   = Carbon::now()->subMonth()->endOfMonth()->toDateString();

        // Use financial year dates instead of calendar year
        $fy        = \App\Models\FinancialYear::current();
        $yearStart = $fy ? $fy->start_date->format('Y-m-d') : Carbon::now()->startOfYear()->toDateString();
        $yearEnd   = $fy ? $fy->end_date->format('Y-m-d') : Carbon::now()->endOfYear()->toDateString();

        // ── Today's Trips ─────────────────────────────────────────────────────
        $todayTrips = Trip::whereDate('trip_date', $today)
            ->where('is_deleted', false)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'planned'   THEN 1 ELSE 0 END) as planned,
                SUM(CASE WHEN status = 'running'   THEN 1 ELSE 0 END) as running,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
            ")
            ->first();

        // ── All-time Trip summary ─────────────────────────────────────────────
        $allTrips = Trip::where('is_deleted', false)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'planned'   THEN 1 ELSE 0 END) as planned,
                SUM(CASE WHEN status = 'running'   THEN 1 ELSE 0 END) as running,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                SUM(CASE WHEN status = 'completed' THEN freight_amount  ELSE 0 END) as total_revenue,
                SUM(CASE WHEN status = 'completed' THEN collected_amount ELSE 0 END) as total_collected,
                SUM(CASE WHEN payment_status != 'completed' AND status = 'completed' THEN balance_amount ELSE 0 END) as total_outstanding
            ")
            ->first();

        // ── This month trips ──────────────────────────────────────────────────
        $monthTrips = Trip::where('is_deleted', false)
            ->whereBetween('trip_date', [$monthStart, $monthEnd])
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'completed' THEN freight_amount ELSE 0 END) as revenue,
                SUM(CASE WHEN status = 'completed' THEN collected_amount ELSE 0 END) as collected
            ")
            ->first();

        // ── Last month trips (for % change) ──────────────────────────────────
        $lastMonthTrips = Trip::where('is_deleted', false)
            ->whereBetween('trip_date', [$lastMonthStart, $lastMonthEnd])
            ->selectRaw("COUNT(*) as total, SUM(CASE WHEN status='completed' THEN freight_amount ELSE 0 END) as revenue")
            ->first();

        // ── Recent trips ──────────────────────────────────────────────────────
        $recentTrips = Trip::where('is_deleted', false)
            ->with(['vehicle', 'driver', 'party'])
            ->orderByDesc('trip_date')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        // ── Running trips detail ──────────────────────────────────────────────
        $runningTrips = Trip::where('is_deleted', false)
            ->where('status', 'running')
            ->with(['vehicle', 'driver', 'party'])
            ->orderByDesc('trip_date')
            ->limit(5)
            ->get();

        // ── Expense Stats ─────────────────────────────────────────────────────
        $expenseToday     = Expense::whereDate('expense_date', $today)->where('is_deleted', false)->sum('amount');
        $expenseYesterday = Expense::whereDate('expense_date', $yesterday)->where('is_deleted', false)->sum('amount');
        $expenseWeek      = Expense::whereBetween('expense_date', [$weekStart, $weekEnd])->where('is_deleted', false)->sum('amount');
        $expenseMonth     = Expense::whereBetween('expense_date', [$monthStart, $monthEnd])->where('is_deleted', false)->sum('amount');
        $expenseLastMonth = Expense::whereBetween('expense_date', [$lastMonthStart, $lastMonthEnd])->where('is_deleted', false)->sum('amount');
        $expenseYear      = Expense::whereBetween('expense_date', [$yearStart, $yearEnd])->where('is_deleted', false)->sum('amount');

        // ── Expense by category this month ────────────────────────────────────
        $expenseByCategory = Expense::where('is_deleted', false)
            ->whereBetween('expense_date', [$monthStart, $monthEnd])
            ->selectRaw("category, SUM(amount) as total")
            ->groupBy('category')
            ->orderByDesc('total')
            ->get()
            ->map(fn($e) => [
                'category' => $e->category,
                'label'    => ucfirst(str_replace('_', ' ', $e->category)),
                'total'    => (float) $e->total,
            ])
            ->toArray();

        // ── Monthly Expenses this year ────────────────────────────────────────
        $rawMonthlyExp = Expense::where('is_deleted', false)
            ->whereBetween('expense_date', [$yearStart, $yearEnd])
            ->selectRaw("EXTRACT(MONTH FROM expense_date)::int as month, SUM(amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // ── Monthly Revenue + Trips this year ─────────────────────────────────
        $rawMonthlyRev = Trip::where('is_deleted', false)
            ->where('status', 'completed')
            ->whereBetween('trip_date', [$yearStart, $yearEnd])
            ->selectRaw("EXTRACT(MONTH FROM trip_date)::int as month, SUM(freight_amount) as revenue, SUM(collected_amount) as collected")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $rawMonthlyTrips = Trip::where('is_deleted', false)
            ->whereBetween('trip_date', [$yearStart, $yearEnd])
            ->selectRaw("EXTRACT(MONTH FROM trip_date)::int as month, COUNT(*) as total, SUM(CASE WHEN status='completed' THEN 1 ELSE 0 END) as completed, SUM(CASE WHEN status='cancelled' THEN 1 ELSE 0 END) as cancelled")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Build 12-month arrays
        $monthLabels         = [];
        $monthlyExpenseArr   = [];
        $monthlyRevenueArr   = [];
        $monthlyCollectedArr = [];
        $monthlyTripArr      = [];
        $monthlyCompletedArr = [];
        $monthlyCancelledArr = [];

        for ($m = 1; $m <= 12; $m++) {
            $lbl                   = Carbon::create(null, $m)->format('M');
            $monthLabels[]         = $lbl;
            $monthlyExpenseArr[]   = round((float)($rawMonthlyExp[$m]  ?? 0), 0);
            $monthlyRevenueArr[]   = round((float)($rawMonthlyRev[$m]->revenue  ?? 0), 0);
            $monthlyCollectedArr[] = round((float)($rawMonthlyRev[$m]->collected ?? 0), 0);
            $monthlyTripArr[]      = (int)($rawMonthlyTrips[$m]->total     ?? 0);
            $monthlyCompletedArr[] = (int)($rawMonthlyTrips[$m]->completed ?? 0);
            $monthlyCancelledArr[] = (int)($rawMonthlyTrips[$m]->cancelled ?? 0);
        }

        // ── EMIs ──────────────────────────────────────────────────────────────
        $upcomingEmis = VehicleEmi::where('is_deleted', false)
            ->where('status', 'active')
            ->where('next_due_date', '>=', $today->toDateString())
            ->where('next_due_date', '<=', $today->copy()->addDays(30)->toDateString())
            ->with('vehicle')
            ->orderBy('next_due_date')
            ->limit(6)
            ->get();

        $overdueEmis = VehicleEmi::where('is_deleted', false)
            ->where('status', 'active')
            ->where('next_due_date', '<', $today->toDateString())
            ->with('vehicle')
            ->orderBy('next_due_date')
            ->get();

        $emiMonthTotal = VehicleEmi::where('is_deleted', false)
            ->where('status', 'active')
            ->whereBetween('next_due_date', [$monthStart, $monthEnd])
            ->sum('emi_amount');

        // ── Vehicles ──────────────────────────────────────────────────────────
        $totalVehicles  = Vehicle::whereNull('deleted_at')->count();
        $activeVehicles = Vehicle::whereNull('deleted_at')->where('status', 'active')->count();

        // ── Counts ────────────────────────────────────────────────────────────
        $totalDrivers = Driver::where('is_deleted', false)->count();
        $totalParties = Party::whereNull('deleted_at')->count();

        // ── Top parties by revenue ────────────────────────────────────────────
        $topParties = Trip::where('trips.is_deleted', false)
            ->where('trips.status', 'completed')
            ->whereBetween('trip_date', [$yearStart, $yearEnd])
            ->join('parties', 'parties.id', '=', 'trips.party_id')
            ->selectRaw("parties.id, COALESCE(NULLIF(parties.company_name,''), parties.name) as party_name, COUNT(*) as trip_count, SUM(trips.freight_amount) as total_revenue")
            ->groupBy('parties.id', 'party_name')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();

        // ── Top vehicles by trips ─────────────────────────────────────────────
        $topVehicles = Trip::where('trips.is_deleted', false)
            ->whereBetween('trip_date', [$yearStart, $yearEnd])
            ->join('vehicles', 'vehicles.id', '=', 'trips.vehicle_id')
            ->selectRaw("vehicles.vehicle_number, COUNT(*) as trip_count, SUM(CASE WHEN trips.status='completed' THEN freight_amount ELSE 0 END) as revenue")
            ->groupBy('vehicles.vehicle_number')
            ->orderByDesc('trip_count')
            ->limit(5)
            ->get();

        // ── % changes ────────────────────────────────────────────────────────
        $tripGrowth    = $this->pctChange($lastMonthTrips->total ?? 0, $monthTrips->total ?? 0);
        $revenueGrowth = $this->pctChange($lastMonthTrips->revenue ?? 0, $monthTrips->revenue ?? 0);
        $expenseGrowth = $this->pctChange($expenseLastMonth, $expenseMonth);

        return view('Index', compact(
            'today',
            'todayTrips',
            'allTrips',
            'monthTrips',
            'recentTrips',
            'runningTrips',
            'expenseToday',
            'expenseYesterday',
            'expenseWeek',
            'expenseMonth',
            'expenseYear',
            'expenseByCategory',
            'monthLabels',
            'monthlyExpenseArr',
            'monthlyRevenueArr',
            'monthlyCollectedArr',
            'monthlyTripArr',
            'monthlyCompletedArr',
            'monthlyCancelledArr',
            'upcomingEmis',
            'overdueEmis',
            'emiMonthTotal',
            'totalVehicles',
            'activeVehicles',
            'totalDrivers',
            'totalParties',
            'topParties',
            'topVehicles',
            'tripGrowth',
            'revenueGrowth',
            'expenseGrowth'
        ));
    }

    private function pctChange(float $old, float $new): array
    {
        if ($old == 0) {
            return ['value' => $new > 0 ? 100 : 0, 'up' => true];
        }
        $pct = round((($new - $old) / $old) * 100, 1);
        return ['value' => abs($pct), 'up' => $pct >= 0];
    }

    public function logout(Request $request)
    {
        $request->session()->flush();

        return redirect()
            ->route('login')
            ->with('logout_success', 'Thank you for using Transport system')
            ->withCookie(AuthController::forgetAuthCookie());
    }
}
