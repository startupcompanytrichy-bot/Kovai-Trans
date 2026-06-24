<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Driver;
use App\Models\Expense;
use App\Models\Party;
use App\Models\Trip;
use App\Models\Vehicle;
use App\Models\VehicleEmi;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('Reports.Reports_Dashboard');
    }

    public function trips(Request $request)
    {
        $query = Trip::with(['vehicle', 'driver', 'party'])
            ->where('is_deleted', false);

        // Use FY filter as default when no manual date range is provided
        if (!$request->filled('date_from') && !$request->filled('date_to')) {
            \applyFinYearFilter($query);
        }

        $this->applyDateFilter($query, $request, 'trip_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }
        if ($request->filled('party_id')) {
            $query->where('party_id', $request->party_id);
        }

        $trips = $query->orderBy('trip_date', 'desc')->get();

        $summary = [
            'total_trips'    => $trips->count(),
            'total_freight'  => $trips->sum('freight_amount'),
            'total_collected' => $trips->sum('collected_amount'),
            'outstanding'    => $trips->sum(fn($t) => $t->outstanding_amount),
            'total_expenses' => $trips->sum(fn($t) => $t->total_expenses),
            'net_profit'     => $trips->sum(fn($t) => $t->net_profit),
        ];

        $drivers  = Driver::where('is_active', true)->where('is_deleted', false)->orderBy('name')->get();
        $vehicles = Vehicle::orderBy('vehicle_number')->get();
        $parties  = Party::orderBy('company_name')->orderBy('name')->get();

        return view('Reports.Trip_Report', compact('trips', 'summary', 'drivers', 'vehicles', 'parties'));
    }

    public function tripsPdf(Request $request)
    {
        $query = Trip::with(['vehicle', 'driver', 'party'])
            ->where('is_deleted', false);

        if (!$request->filled('date_from') && !$request->filled('date_to')) {
            \applyFinYearFilter($query);
        }

        $this->applyDateFilter($query, $request, 'trip_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        $trips = $query->orderBy('trip_date', 'desc')->get();

        $summary = [
            'total_trips'     => $trips->count(),
            'total_freight'   => $trips->sum('freight_amount'),
            'total_collected' => $trips->sum('collected_amount'),
            'outstanding'     => $trips->sum(fn($t) => $t->outstanding_amount),
            'total_expenses'  => $trips->sum(fn($t) => $t->total_expenses),
            'net_profit'      => $trips->sum(fn($t) => $t->net_profit),
        ];

        $filters = $request->only(['date_from', 'date_to', 'status', 'driver_id', 'vehicle_id']);

        return view('Reports.Trip_Report_Print', compact('trips', 'summary', 'filters'));
    }

    public function expenses(Request $request)
    {
        $query = Expense::with(['trip', 'vehicle', 'driver'])
            ->where('is_deleted', false);

        // Use FY filter as default when no manual date range is provided
        if (!$request->filled('date_from') && !$request->filled('date_to')) {
            \applyFinYearFilter($query);
        }

        $this->applyDateFilter($query, $request, 'expense_date');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->get();

        $summary = [
            'total'       => $expenses->sum('amount'),
            'by_category' => $expenses->groupBy('category')->map(fn($g) => $g->sum('amount')),
        ];

        $vehicles   = Vehicle::orderBy('vehicle_number')->get();
        $categories = Expense::$categories;

        return view('Reports.Expense_Report', compact('expenses', 'summary', 'vehicles', 'categories'));
    }

    public function pnl(Request $request)
    {
        $query = Trip::with(['vehicle', 'driver', 'party'])
            ->where('is_deleted', false)
            ->where('status', 'completed');

        // Use FY filter as default when no manual date range is provided
        if (!$request->filled('date_from') && !$request->filled('date_to')) {
            \applyFinYearFilter($query);
        }

        $this->applyDateFilter($query, $request, 'trip_date');

        $trips = $query->orderBy('trip_date', 'desc')->get();

        $summary = [
            'total_trips'    => $trips->count(),
            'total_income'   => $trips->sum('freight_amount'),
            'total_expenses' => $trips->sum(fn($t) => $t->total_expenses),
            'net_profit'     => $trips->sum(fn($t) => $t->net_profit),
            'profit_trips'   => $trips->filter(fn($t) => $t->is_profitable)->count(),
            'loss_trips'     => $trips->filter(fn($t) => !$t->is_profitable)->count(),
        ];

        return view('Reports.PnL_Report', compact('trips', 'summary'));
    }

    public function collection(Request $request)
    {
        $query = Trip::with(['party', 'vehicle'])
            ->where('is_deleted', false)
            ->where('payment_status', '!=', 'completed');

        // Use FY filter as default when no manual date range is provided
        if (!$request->filled('date_from') && !$request->filled('date_to')) {
            \applyFinYearFilter($query);
        }

        $this->applyDateFilter($query, $request, 'trip_date');

        $trips = $query->orderBy('collection_due_date')->get();

        $summary = [
            'total_outstanding' => $trips->sum(fn($t) => $t->outstanding_amount),
            'overdue'           => $trips->filter(fn($t) => $t->collection_due_date && $t->collection_due_date->isPast())->count(),
            'pending_count'     => $trips->count(),
        ];

        return view('Reports.Collection_Report', compact('trips', 'summary'));
    }

    private function buildEmiRows($emis)
    {
        return $emis->flatMap(function ($emi) {
            $payByMonth = $emi->payments->keyBy(fn($p) => optional($p->due_month)->format('Y-m'));
            $rows = [];
            $today = \Carbon\Carbon::today();

            if ($emi->loan_start_date && ($emi->total_emis || $emi->loan_end_date)) {
                // ── Build full projected schedule ──
                if ($emi->first_instalment_date) {
                    $firstDue = $emi->first_instalment_date->copy();
                } elseif ($emi->next_due_date && $emi->paid_emis > 0) {
                    $firstDue = $emi->next_due_date->copy()->subMonths($emi->paid_emis);
                } else {
                    $firstDue = $emi->loan_start_date->copy()->addMonth();
                }

                $total   = $emi->total_emis ?? (int) $emi->loan_start_date->diffInMonths($emi->loan_end_date);
                $balance = (float) $emi->loan_amount;

                for ($i = 1; $i <= $total; $i++) {
                    $due     = $firstDue->copy()->addMonths($i - 1);
                    $mkey    = $due->format('Y-m');
                    $payment = $payByMonth->get($mkey);
                    $balance = max(0, $balance - (float) $emi->emi_amount);

                    if ($payment) {
                        $st = 'paid';
                    } elseif ($due->format('Y-m') < $today->format('Y-m')) {
                        $st = 'over';
                    } elseif ($due->format('Y-m') === $today->format('Y-m')) {
                        $st = 'curr';
                    } elseif ($due->diffInDays($today) <= 30) {
                        $st = 'soon';
                    } else {
                        $st = 'pend';
                    }

                    $rows[] = (object) [
                        'emi'             => $emi,
                        'vehicle'         => $emi->vehicle,
                        'financier_name'  => $emi->financier_name,
                        'inst_no'         => $i,
                        'due_date'        => $due,
                        'mkey'            => $mkey,
                        'payment'         => $payment,
                        'balance'         => $balance,
                        'status'          => $st,
                        'emi_amount'      => (float) $emi->emi_amount,
                    ];
                }
            } elseif ($emi->payments->isNotEmpty()) {
                // ── Fallback: show actual payments when schedule data is missing ──
                foreach ($emi->payments->sortBy('payment_date') as $p) {
                    $due = $p->due_month ?? $p->payment_date;
                    $rows[] = (object) [
                        'emi'             => $emi,
                        'vehicle'         => $emi->vehicle,
                        'financier_name'  => $emi->financier_name,
                        'inst_no'         => '—',
                        'due_date'        => $due,
                        'mkey'            => optional($due)->format('Y-m') ?? '',
                        'payment'         => $p,
                        'balance'         => 0,
                        'status'          => 'paid',
                        'emi_amount'      => (float) $emi->emi_amount,
                    ];
                }
            } else {
                // ── Minimal fallback: one summary row per loan ──
                $rows[] = (object) [
                    'emi'             => $emi,
                    'vehicle'         => $emi->vehicle,
                    'financier_name'  => $emi->financier_name,
                    'inst_no'         => '—',
                    'due_date'        => $emi->next_due_date ?? $emi->loan_start_date,
                    'mkey'            => '',
                    'payment'         => null,
                    'balance'         => (float) $emi->outstanding_balance,
                    'status'          => $emi->is_overdue ? 'over' : ($emi->status === 'active' ? 'pend' : $emi->status),
                    'emi_amount'      => (float) $emi->emi_amount,
                ];
            }
            return $rows;
        })->values();
    }

    public function emi(Request $request)
    {
        $query = VehicleEmi::with(['vehicle', 'payments'])
            ->where('is_deleted', false);

        \applyFinYearFilter($query);
        $this->applyDateFilter($query, $request, 'next_due_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }
        if ($request->filled('financier')) {
            $query->where('financier_name', 'like', '%' . $request->financier . '%');
        }

        $emis = $query->orderBy('financier_name')->orderBy('next_due_date')->get();

        $summary = [
            'total_loans'            => $emis->count(),
            'total_loan_amount'      => $emis->sum('loan_amount'),
            'total_interest_amount'  => $emis->sum('interest_amount'),
            'total_amount'           => $emis->sum(fn($e) => ($e->loan_amount ?? 0) + ($e->interest_amount ?? 0)),
            'total_emis'             => $emis->sum('total_emis'),
            'total_paid_emis'        => $emis->sum('paid_emis'),
            'total_balance_emis'     => $emis->sum(fn($e) => ($e->total_emis ?? 0) - ($e->paid_emis ?? 0)),
            'total_due_amount'       => $emis->sum(fn($e) => (($e->total_emis ?? 0) - ($e->paid_emis ?? 0)) * (float) ($e->emi_amount ?? 0)),
            'total_paid_amount'      => $emis->sum(fn($e) => (float) $e->payments->sum('amount_paid')),
            'total_balance_amount'   => $emis->sum(fn($e) => (($e->loan_amount ?? 0) + ($e->interest_amount ?? 0)) - (float) $e->payments->sum('amount_paid')),
            'total_payable'          => $emis->sum('total_payable'),
            'total_paid'             => $emis->sum(fn($e) => (float) $e->payments->sum('amount_paid')),
            'total_outstanding'      => $emis->sum('outstanding_balance'),
            'total_emi_monthly'      => $emis->sum('emi_amount'),
            'overdue_count'          => $emis->filter(fn($e) => $e->is_overdue)->count(),
        ];

        $vehicles   = Vehicle::orderBy('vehicle_number')->get();
        $financiersQuery = VehicleEmi::where('is_deleted', false)
            ->whereNotNull('financier_name');

        \applyFinYearFilter($financiersQuery);
        $financiers = $financiersQuery->distinct('financier_name')->pluck('financier_name');

        return view('Reports.Emi_Report', compact('emis', 'summary', 'vehicles', 'financiers'));
    }

    public function emiPdf(Request $request)
    {
        $query = VehicleEmi::with(['vehicle', 'payments'])
            ->where('is_deleted', false);

        \applyFinYearFilter($query);
        $this->applyDateFilter($query, $request, 'next_due_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }
        if ($request->filled('financier')) {
            $query->where('financier_name', 'like', '%' . $request->financier . '%');
        }

        $emis = $query->orderBy('financier_name')->orderBy('next_due_date')->get();

        $summary = [
            'total_loans'            => $emis->count(),
            'total_loan_amount'      => $emis->sum('loan_amount'),
            'total_interest_amount'  => $emis->sum('interest_amount'),
            'total_amount'           => $emis->sum(fn($e) => ($e->loan_amount ?? 0) + ($e->interest_amount ?? 0)),
            'total_emis'             => $emis->sum('total_emis'),
            'total_paid_emis'        => $emis->sum('paid_emis'),
            'total_balance_emis'     => $emis->sum(fn($e) => ($e->total_emis ?? 0) - ($e->paid_emis ?? 0)),
            'total_due_amount'       => $emis->sum(fn($e) => (($e->total_emis ?? 0) - ($e->paid_emis ?? 0)) * (float) ($e->emi_amount ?? 0)),
            'total_paid_amount'      => $emis->sum(fn($e) => (float) $e->payments->sum('amount_paid')),
            'total_balance_amount'   => $emis->sum(fn($e) => (($e->loan_amount ?? 0) + ($e->interest_amount ?? 0)) - (float) $e->payments->sum('amount_paid')),
            'total_payable'          => $emis->sum('total_payable'),
            'total_paid'             => $emis->sum(fn($e) => (float) $e->payments->sum('amount_paid')),
            'total_outstanding'      => $emis->sum('outstanding_balance'),
            'total_emi_monthly'      => $emis->sum('emi_amount'),
        ];

        $filters = $request->only(['date_from', 'date_to', 'status', 'vehicle_id', 'financier']);

        return view('Reports.Emi_Report_Print', compact('emis', 'summary', 'filters'));
    }

    public function emiExcel(Request $request)
    {
        $query = VehicleEmi::with(['vehicle', 'payments'])
            ->where('is_deleted', false);

        \applyFinYearFilter($query);
        $this->applyDateFilter($query, $request, 'next_due_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }
        if ($request->filled('financier')) {
            $query->where('financier_name', 'like', '%' . $request->financier . '%');
        }

        $emis = $query->orderBy('financier_name')->orderBy('next_due_date')->get();

        $filename = 'EMI_Details_Report_' . now()->format('Ymd_His') . '.csv';

        return response()->stream(function () use ($emis) {
            $h = fopen('php://output', 'w');
            fputcsv($h, [
                '#',
                'Vehicle Register Number',
                'Financier',
                'Loan Amount (₹)',
                'Interest (₹)',
                'Total Amount (₹)',
                'Total EMIs',
                'Total Number of Dues',
                'Next Due Date',
                'Monthly EMI (₹)',
                'Total Paid Amount (₹)',
                'Balance Amount (₹)',
                'Loan Start Date',
                'Loan End Date'
            ]);
            foreach ($emis as $i => $e) {
                $totalPaid = (float) $e->payments->sum('amount_paid');
                $totalAmount = ($e->loan_amount ?? 0) + ($e->interest_amount ?? 0);
                $balanceAmount = $totalAmount - $totalPaid;
                $balanceEmis = ($e->total_emis ?? 0) - ($e->paid_emis ?? 0);
                fputcsv($h, [
                    $i + 1,
                    optional($e->vehicle)->vehicle_number ?? '—',
                    $e->financier_name ?? '—',
                    number_format($e->loan_amount, 2),
                    number_format($e->interest_amount ?? 0, 2),
                    number_format($totalAmount, 2),
                    $e->total_emis ?? 0,
                    $balanceEmis,
                    $e->next_due_date ? $e->next_due_date->format('d/m/Y') : '—',
                    number_format($e->emi_amount, 2),
                    number_format($totalPaid, 2),
                    number_format($balanceAmount, 2),
                    $e->loan_start_date ? $e->loan_start_date->format('d/m/Y') : '—',
                    $e->loan_end_date ? $e->loan_end_date->format('d/m/Y') : '—',
                ]);
            }
            fputcsv($h, [
                '',
                'TOTAL',
                '',
                number_format($emis->sum('loan_amount'), 2),
                number_format($emis->sum('interest_amount'), 2),
                number_format($emis->sum(fn($e) => ($e->loan_amount ?? 0) + ($e->interest_amount ?? 0)), 2),
                $emis->sum('total_emis'),
                $emis->sum(fn($e) => ($e->total_emis ?? 0) - ($e->paid_emis ?? 0)),
                '',
                number_format($emis->sum('emi_amount'), 2),
                number_format($emis->sum(fn($e) => (float) $e->payments->sum('amount_paid')), 2),
                number_format($emis->sum(fn($e) => (($e->loan_amount ?? 0) + ($e->interest_amount ?? 0)) - (float) $e->payments->sum('amount_paid')), 2),
                '',
                ''
            ]);
            fclose($h);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function partiesPaymentLedger(Request $request)
    {
        $company = Company::where('is_deleted', false)->first();

        $query = Trip::with(['party', 'vehicle', 'driver', 'payments'])
            ->where('is_deleted', false)
            ->where('invoice_status', 'invoiced');

        if ($request->filled('party_id')) {
            $query->where('party_id', $request->party_id);
        }

        // Default to June 2026 when no date filter is set
        if (!$request->filled('date_type') && !$request->filled('date_from') && !$request->filled('date_to')) {
            $query->whereMonth('trip_date', 6)->whereYear('trip_date', 2026);
            $filterLabel = 'Month: June 2026';
        } elseif ($request->filled('date_type')) {
            switch ($request->date_type) {
                case 'month':
                    if ($request->filled('month')) {
                        $query->whereMonth('trip_date', date('m', strtotime($request->month)))
                            ->whereYear('trip_date', date('Y', strtotime($request->month)));
                        $filterLabel = 'Month: ' . date('F Y', strtotime($request->month));
                    } else {
                        $filterLabel = 'Financial Year';
                    }
                    break;
                case 'year':
                    if ($request->filled('year')) {
                        $query->whereYear('trip_date', $request->year);
                        $filterLabel = 'Year: ' . $request->year;
                    } else {
                        $filterLabel = 'Financial Year';
                    }
                    break;
                case 'date':
                    if ($request->filled('exact_date')) {
                        $query->whereDate('trip_date', $request->exact_date);
                        $filterLabel = 'Date: ' . date('d M Y', strtotime($request->exact_date));
                    } else {
                        $filterLabel = 'Financial Year';
                    }
                    break;
                case 'range':
                    $this->applyDateFilter($query, $request, 'trip_date');
                    $from = $request->date_from ? date('d M Y', strtotime($request->date_from)) : '—';
                    $to   = $request->date_to   ? date('d M Y', strtotime($request->date_to))   : '—';
                    $filterLabel = $from . ' — ' . $to;
                    break;
                default:
                    if (!$request->filled('date_from') && !$request->filled('date_to')) {
                        \applyFinYearFilter($query);
                    }
                    $this->applyDateFilter($query, $request, 'trip_date');
                    $filterLabel = 'Financial Year';
                    break;
            }
        } else {
            if (!$request->filled('date_from') && !$request->filled('date_to')) {
                \applyFinYearFilter($query);
            }
            $this->applyDateFilter($query, $request, 'trip_date');
            $filterLabel = 'All Dates';
        }

        $trips = $query->orderBy('trip_date')->get();

        // Build ledger entries: one invoice row + payment rows per trip
        $entries = collect();
        foreach ($trips as $trip) {
            $invNo   = $trip->invoice_no ?: $trip->trip_no;
            $invDate = $trip->invoiced_at ?? $trip->trip_date;
            $invDateStr = $invDate ? $invDate->format('Y-m-d') : '';

            $entries->push((object) [
                'date'             => $invDate,
                'transaction_type' => 'Invoice',
                'details'          => $invNo,
                'amount'           => (float) $trip->balance_amount,
                'payment'          => 0,
                'party'            => $trip->party,
                'vehicle'          => $trip->vehicle,
                'sort_date'        => $invDateStr,
                'sort_order'       => 0,
                'inv_date'         => $invDateStr,
                'inv_no'           => $invNo,
            ]);

            $collectedAmt = (float) $trip->collected_amount;
            if ($collectedAmt > 0) {
                $pmtDate = $trip->collection_due_date ?? $trip->invoiced_at ?? $trip->trip_date;
                $entries->push((object) [
                    'date'             => $pmtDate,
                    'transaction_type' => 'Payment Received',
                    'details'          => 'Invoice #' . $invNo . ' Payment of ₹' . number_format($collectedAmt),
                    'amount'           => 0,
                    'payment'          => $collectedAmt,
                    'party'            => $trip->party,
                    'vehicle'          => $trip->vehicle,
                    'sort_date'        => $pmtDate ? $pmtDate->format('Y-m-d') : '',
                    'sort_order'       => 1,
                    'inv_date'         => $invDateStr,
                    'inv_no'           => $invNo,
                ]);
            }
        }

        // Sort by invoice date, then invoice number, then invoice before payment
        $entries = $entries->sort(function ($a, $b) {
            $cmp = strcmp($a->inv_date, $b->inv_date);
            if ($cmp !== 0) return $cmp;
            $cmp = strcmp($a->inv_no, $b->inv_no);
            if ($cmp !== 0) return $cmp;
            return $a->sort_order <=> $b->sort_order;
        })->values();

        // Calculate running balance
        $balance = 0;
        $entries = $entries->map(function ($e) use (&$balance) {
            $balance += $e->amount - $e->payment;
            $e->balance = $balance;
            return $e;
        });

        $totalAmount   = $entries->sum('amount');
        $totalPayment  = $entries->sum('payment');
        $totalBalance  = $entries->last() ? $entries->last()->balance : 0;

        $parties = Party::orderBy('company_name')->orderBy('name')->get();
        $selectedParty = $request->filled('party_id') ? ($entries->first() ? $entries->first()->party : null) : null;

        return view('Reports.Parties_Payment_Ledger', compact('entries', 'totalAmount', 'totalPayment', 'totalBalance', 'parties', 'company', 'filterLabel', 'selectedParty'));
    }

    public function partiesPaymentLedgerPdf(Request $request)
    {
        $company = Company::where('is_deleted', false)->first();

        $query = Trip::with(['party', 'vehicle', 'driver', 'payments'])
            ->where('is_deleted', false)
            ->where('invoice_status', 'invoiced');

        if ($request->filled('party_id')) {
            $query->where('party_id', $request->party_id);
        }

        if (!$request->filled('date_type') && !$request->filled('date_from') && !$request->filled('date_to')) {
            $query->whereMonth('trip_date', 6)->whereYear('trip_date', 2026);
            $filterLabel = 'Month: June 2026';
        } elseif ($request->filled('date_type')) {
            switch ($request->date_type) {
                case 'month':
                    if ($request->filled('month')) {
                        $query->whereMonth('trip_date', date('m', strtotime($request->month)))
                            ->whereYear('trip_date', date('Y', strtotime($request->month)));
                        $filterLabel = 'Month: ' . date('F Y', strtotime($request->month));
                    } else {
                        $filterLabel = 'Financial Year';
                    }
                    break;
                case 'year':
                    if ($request->filled('year')) {
                        $query->whereYear('trip_date', $request->year);
                        $filterLabel = 'Year: ' . $request->year;
                    } else {
                        $filterLabel = 'Financial Year';
                    }
                    break;
                case 'date':
                    if ($request->filled('exact_date')) {
                        $query->whereDate('trip_date', $request->exact_date);
                        $filterLabel = 'Date: ' . date('d M Y', strtotime($request->exact_date));
                    } else {
                        $filterLabel = 'Financial Year';
                    }
                    break;
                case 'range':
                    $this->applyDateFilter($query, $request, 'trip_date');
                    $from = $request->date_from ? date('d M Y', strtotime($request->date_from)) : '—';
                    $to   = $request->date_to   ? date('d M Y', strtotime($request->date_to))   : '—';
                    $filterLabel = $from . ' — ' . $to;
                    break;
                default:
                    if (!$request->filled('date_from') && !$request->filled('date_to')) {
                        \applyFinYearFilter($query);
                    }
                    $this->applyDateFilter($query, $request, 'trip_date');
                    $filterLabel = 'Financial Year';
                    break;
            }
        } else {
            if (!$request->filled('date_from') && !$request->filled('date_to')) {
                \applyFinYearFilter($query);
            }
            $this->applyDateFilter($query, $request, 'trip_date');
            $filterLabel = 'All Dates';
        }

        $trips = $query->orderBy('trip_date')->get();

        $entries = collect();
        foreach ($trips as $trip) {
            $invNo   = $trip->invoice_no ?: $trip->trip_no;
            $invDate = $trip->invoiced_at ?? $trip->trip_date;
            $invDateStr = $invDate ? $invDate->format('Y-m-d') : '';

            $entries->push((object) [
                'date'             => $invDate,
                'transaction_type' => 'Invoice',
                'details'          => $invNo,
                'amount'           => (float) $trip->balance_amount,
                'payment'          => 0,
                'party'            => $trip->party,
                'vehicle'          => $trip->vehicle,
                'sort_date'        => $invDateStr,
                'sort_order'       => 0,
                'inv_date'         => $invDateStr,
                'inv_no'           => $invNo,
            ]);

            $collectedAmt = (float) $trip->collected_amount;
            if ($collectedAmt > 0) {
                $pmtDate = $trip->collection_due_date ?? $trip->invoiced_at ?? $trip->trip_date;
                $entries->push((object) [
                    'date'             => $pmtDate,
                    'transaction_type' => 'Payment Received',
                    'details'          => 'Invoice #' . $invNo . ' Payment of ₹' . number_format($collectedAmt),
                    'amount'           => 0,
                    'payment'          => $collectedAmt,
                    'party'            => $trip->party,
                    'vehicle'          => $trip->vehicle,
                    'sort_date'        => $pmtDate ? $pmtDate->format('Y-m-d') : '',
                    'sort_order'       => 1,
                    'inv_date'         => $invDateStr,
                    'inv_no'           => $invNo,
                ]);
            }
        }

        $entries = $entries->sort(function ($a, $b) {
            $cmp = strcmp($a->inv_date, $b->inv_date);
            if ($cmp !== 0) return $cmp;
            $cmp = strcmp($a->inv_no, $b->inv_no);
            if ($cmp !== 0) return $cmp;
            return $a->sort_order <=> $b->sort_order;
        })->values();

        $balance = 0;
        $entries = $entries->map(function ($e) use (&$balance) {
            $balance += $e->amount - $e->payment;
            $e->balance = $balance;
            return $e;
        });

        $totalAmount   = $entries->sum('amount');
        $totalPayment  = $entries->sum('payment');
        $totalBalance  = $entries->last() ? $entries->last()->balance : 0;

        $selectedParty = $request->filled('party_id') ? ($entries->first() ? $entries->first()->party : null) : null;

        return view('Reports.Parties_Payment_Ledger_Pdf', compact('entries', 'totalAmount', 'totalPayment', 'totalBalance', 'company', 'filterLabel', 'selectedParty'));
    }

    private function applyDateFilter($query, Request $request, string $column): void
    {
        if ($request->filled('date_from')) {
            $query->whereDate($column, '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate($column, '<=', $request->date_to);
        }
    }

    /* ── Shared invoice query builder ─────────────────────────────── */
    private function invoiceQuery(Request $request)
    {
        $query = Trip::with(['party'])
            ->where('is_deleted', false)
            ->where('invoice_status', 'invoiced')
            ->whereNotNull('invoice_no');

        if (!$request->filled('date_from') && !$request->filled('date_to')) {
            \applyFinYearFilter($query);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('invoiced_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('invoiced_at', '<=', $request->date_to);
        }
        if ($request->filled('invoice_type')) {
            $query->where('invoice_type', $request->invoice_type);
        }

        // Default to showing only payment-collected (completed) invoices
        // unless the user explicitly chooses a different status (including 'all')
        $paymentStatus = $request->input('payment_status', 'completed');
        if ($paymentStatus !== '' && $paymentStatus !== 'all') {
            $query->where('payment_status', $paymentStatus);
        }

        return $query->orderBy('invoiced_at', 'desc');
    }

    private function buildInvoiceRows($trips): \Illuminate\Support\Collection
    {
        return $trips->groupBy('invoice_no')->map(function ($group, $invoiceNo) {
            $first      = $group->first();
            $freight    = (float) $group->sum('freight_amount');
            $type       = $first->invoice_type ?? 'normal';
            [$cgR, $sgR] = match ($type) {
                'rcm'    => [2.5, 2.5],
                'exempt' => [0.0, 0.0],
                default  => [9.0, 9.0],
            };
            $tax        = round($freight * ($cgR + $sgR) / 100, 2);
            $collected  = (float) $group->sum('collected_amount');
            $payStatus  = 'pending';
            $statuses   = $group->pluck('payment_status')->unique();
            if ($statuses->contains('completed') && $statuses->count() === 1) {
                $payStatus = 'completed';
            } elseif ($statuses->contains('completed') || $statuses->contains('partial')) {
                $payStatus = 'partial';
            }
            $collDate = $group->whereNotNull('collection_due_date')
                ->sortByDesc('collection_due_date')->first()?->collection_due_date;

            return (object) [
                'invoice_no'          => $invoiceNo,
                'party_name'          => optional($first->party)->company_name ?: optional($first->party)->name ?? '—',
                'invoice_type'        => $type,
                'invoiced_at'         => $first->invoiced_at,
                'trip_count'          => $group->count(),
                'freight'             => $freight,
                'tax'                 => $tax,
                'grand_total'         => $freight + $tax,
                'collected_amount'    => $collected,
                'balance'             => max(0, $freight - $collected),
                'payment_status'      => $payStatus,
                'collection_due_date' => $collDate,
            ];
        })->values();
    }

    public function invoices(Request $request)
    {
        $trips = $this->invoiceQuery($request)->get();
        $rows  = $this->buildInvoiceRows($trips);

        // Group raw trips by invoice_no for the per-invoice trip detail print
        $tripsByInvoice = $trips->groupBy('invoice_no')->map(function ($group) {
            return $group->map(function ($t) {
                return (object) [
                    'trip_no'        => $t->trip_no,
                    'trip_date'      => $t->trip_date ? $t->trip_date->format('d M Y') : '—',
                    'vehicle'        => optional($t->vehicle)->vehicle_number ?? '—',
                    'driver'         => optional($t->driver)->name ?? '—',
                    'from_location'  => $t->from_location ?? '—',
                    'to_location'    => $t->to_location ?? '—',
                    'lr_no'          => $t->lr_no ?? '',
                    'freight_amount' => (float) $t->freight_amount,
                    'payment_status' => $t->payment_status ?? 'pending',
                ];
            })->values();
        });

        $summary = [
            'total_invoices'   => $rows->count(),
            'total_freight'    => $rows->sum('freight'),
            'total_tax'        => $rows->sum('tax'),
            'total_grand'      => $rows->sum('grand_total'),
            'total_collected'  => $rows->sum('collected_amount'),
            'total_balance'    => $rows->sum('balance'),
            'completed_count'  => $rows->where('payment_status', 'completed')->count(),
            'pending_count'    => $rows->where('payment_status', 'pending')->count(),
        ];

        return view('Reports.Invoice_Report', compact('rows', 'summary', 'tripsByInvoice'));
    }

    public function invoicesPdf(Request $request)
    {
        $trips = $this->invoiceQuery($request)->get();
        $rows  = $this->buildInvoiceRows($trips);
        $summary = [
            'total_invoices'  => $rows->count(),
            'total_freight'   => $rows->sum('freight'),
            'total_tax'       => $rows->sum('tax'),
            'total_grand'     => $rows->sum('grand_total'),
            'total_collected' => $rows->sum('collected_amount'),
            'total_balance'   => $rows->sum('balance'),
        ];
        $filters = $request->only(['date_from', 'date_to', 'invoice_type', 'payment_status']);

        return view('Reports.Invoice_Report_Print', compact('rows', 'summary', 'filters'));
    }

    public function invoicesPrintSelected(Request $request)
    {
        $request->validate([
            'invoice_nos'   => 'required|array|min:1',
            'invoice_nos.*' => 'string',
        ]);

        $invoiceNos = $request->input('invoice_nos');
        $company    = \App\Models\Company::where('is_deleted', false)->first();

        // Load each invoice's trips, grouped by invoice_no in the requested order
        $allTrips = Trip::with(['vehicle', 'driver', 'party', 'supplier'])
            ->whereIn('invoice_no', $invoiceNos)
            ->where('is_deleted', false)
            ->orderBy('invoice_no')
            ->orderBy('trip_date')
            ->get()
            ->groupBy('invoice_no');

        // Build invoice objects in the order the user selected
        $invoices = collect($invoiceNos)->map(function ($invNo) use ($allTrips) {
            $trips = $allTrips->get($invNo);
            if (!$trips || $trips->isEmpty()) return null;

            $first       = $trips->first();
            $invoiceType = $first->invoice_type ?? 'normal';
            $typeName    = match ($invoiceType) {
                'rcm'    => 'RCM INVOICE',
                'exempt' => 'EXEMPTED INVOICE',
                default  => 'TAX INVOICE',
            };

            return (object) [
                'invoice_no'   => $invNo,
                'trips'        => $trips,
                'invoice_type' => $invoiceType,
                'type_name'    => $typeName,
            ];
        })->filter()->values();

        return view('Reports.Invoice_Print_Selected', compact('invoices', 'company'));
    }

    public function invoicesExcel(Request $request)
    {
        $trips = $this->invoiceQuery($request)->get();
        $rows  = $this->buildInvoiceRows($trips);

        $filename = 'Invoice_Report_' . now()->format('Ymd_His') . '.csv';

        return response()->stream(function () use ($rows) {
            $h = fopen('php://output', 'w');
            fputcsv($h, [
                '#',
                'Invoice No',
                'Party / Client',
                'Type',
                'Invoice Date',
                'Payment Collected Date',
                'Trips',
                'Freight (₹)',
                'Tax (₹)',
                'Grand Total (₹)',
                'Collected (₹)',
                'Balance (₹)',
                'Payment Status'
            ]);
            foreach ($rows as $i => $r) {
                fputcsv($h, [
                    $i + 1,
                    $r->invoice_no,
                    $r->party_name,
                    strtoupper($r->invoice_type),
                    $r->invoiced_at ? $r->invoiced_at->format('d/m/Y') : '—',
                    $r->collection_due_date ? \Carbon\Carbon::parse($r->collection_due_date)->format('d/m/Y') : '—',
                    $r->trip_count,
                    number_format($r->freight, 2),
                    number_format($r->tax, 2),
                    number_format($r->grand_total, 2),
                    number_format($r->collected_amount, 2),
                    number_format($r->balance, 2),
                    ucfirst($r->payment_status),
                ]);
            }
            // Totals row
            fputcsv($h, [
                '',
                'TOTAL',
                '',
                '',
                '',
                '',
                $rows->sum('trip_count'),
                number_format($rows->sum('freight'), 2),
                number_format($rows->sum('tax'), 2),
                number_format($rows->sum('grand_total'), 2),
                number_format($rows->sum('collected_amount'), 2),
                number_format($rows->sum('balance'), 2),
                ''
            ]);
            fclose($h);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
