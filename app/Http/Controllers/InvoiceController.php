<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * GET /invoice
     * Invoice list page — one row per unique invoice_no
     */
    public function index()
    {
        // Group trips by invoice_no, pick first trip for party/date info
        $invoicesQuery = Trip::with(['party'])
            ->where('invoice_status', 'invoiced')
            ->whereNotNull('invoice_no')
            ->where('is_deleted', false);

        \applyFinYearFilter($invoicesQuery);

        $invoices = $invoicesQuery->orderBy('invoiced_at', 'desc')->get()
            ->groupBy('invoice_no')
            ->map(function ($trips, $invoiceNo) {
                $first = $trips->first();
                // Aggregate payment: if any trip is partial → partial; all completed → completed; else pending
                $statuses = $trips->pluck('payment_status')->unique()->values();
                $payStatus = 'pending';
                if ($statuses->contains('completed') && $statuses->count() === 1) {
                    $payStatus = 'completed';
                } elseif ($statuses->contains('completed') || $statuses->contains('partial')) {
                    $payStatus = 'partial';
                }
                $collectedDate = $trips->whereNotNull('collection_due_date')->sortByDesc('collection_due_date')->first()?->collection_due_date;
                $collectedAmt  = $trips->sum('collected_amount');

                $payMode = $trips->whereNotNull('payment_mode')->sortByDesc('updated_at')->first()?->payment_mode;

                return (object) [
                    'invoice_no'          => $invoiceNo,
                    'invoice_type'        => $first->invoice_type ?? 'normal',
                    'invoiced_at'         => $first->invoiced_at,
                    'party_name'          => optional($first->party)->company_name ?: optional($first->party)->name,
                    'trip_count'          => $trips->count(),
                    'subtotal'            => $trips->sum('freight_amount'),
                    'payment_status'      => $payStatus,
                    'collected_amount'    => $collectedAmt,
                    'collection_due_date' => $collectedDate,
                    'payment_mode'        => $payMode,
                    'trip_ids'            => $trips->pluck('id')->toArray(),
                ];
            })
            ->values();

        $company = Company::where('is_deleted', false)->first();

        return view('Invoice.Invoice_List', compact('invoices', 'company'));
    }

    /**
     * GET /invoice/view/{invoiceNo}
     * Show all trips under a specific invoice number — full Zoho-style view
     */
    public function viewInvoice(string $invoiceNo)
    {
        $trips = Trip::with(['vehicle', 'driver', 'party', 'supplier'])
            ->where('invoice_no', $invoiceNo)
            ->where('is_deleted', false)
            ->orderBy('trip_date')
            ->get();

        if ($trips->isEmpty()) {
            abort(404, 'Invoice not found.');
        }

        $company         = Company::where('is_deleted', false)->first();
        $invoiceType     = $trips->first()->invoice_type ?? 'normal';
        $invoiceTypeName = match ($invoiceType) {
            'rcm'    => 'RCM INVOICE',
            'exempt' => 'EXEMPTED INVOICE',
            default  => 'TAX INVOICE',
        };

        return view('Invoice.Trip_Invoice', compact('trips', 'company', 'invoiceNo', 'invoiceType', 'invoiceTypeName'));
    }

    /**
     * Single-trip invoice view (legacy direct link)
     */
    public function trip($id)
    {
        $trip    = Trip::with(['vehicle', 'driver', 'party', 'supplier'])->findOrFail($id);
        $company = Company::where('is_deleted', false)->first();

        return view('Invoice.Trip_Invoice', compact('trip', 'company'));
    }

    /**
     * Single-trip print view
     */
    public function print($id)
    {
        $trip    = Trip::with(['vehicle', 'driver', 'party', 'supplier'])->findOrFail($id);
        $company = Company::where('is_deleted', false)->first();

        return view('Invoice.Trip_Invoice_Print', compact('trip', 'company'));
    }

    /**
     * Multi-trip invoice POST (legacy, no invoice number saved)
     */
    public function multi(Request $request)
    {
        $ids   = $request->input('trip_ids', []);
        $trips = Trip::with(['vehicle', 'driver', 'party', 'supplier'])
            ->whereIn('id', $ids)
            ->orderBy('trip_date')
            ->get();
        $company = Company::where('is_deleted', false)->first();

        return view('Invoice.Trip_Invoice', compact('trips', 'company'));
    }

    /**
     * POST /invoice/generate
     * Marks selected trips as invoiced, assigns invoice number, returns invoice view.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'trip_ids'     => 'required|array|min:1',
            'trip_ids.*'   => 'integer|exists:trips,id',
            'invoice_type' => 'required|in:normal,rcm,exempt',
        ]);

        $ids         = $request->input('trip_ids');
        $invoiceType = $request->input('invoice_type');

        // Sequential invoice number: INV-YYYYMM-NNNN (PostgreSQL-compatible)
        $prefix    = 'INV';
        $yearMonth = now()->format('Ym');

        $lastSeq = Trip::whereNotNull('invoice_no')
            ->where('invoice_no', 'like', "$prefix-$yearMonth-%")
            ->max(DB::raw("CAST(SPLIT_PART(invoice_no, '-', 3) AS INTEGER)"));

        $seq       = ($lastSeq ?? 0) + 1;
        $invoiceNo = $prefix . '-' . $yearMonth . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);

        [$cgstRate, $sgstRate] = match ($invoiceType) {
            'rcm'    => [2.5, 2.5],
            'exempt' => [0.0, 0.0],
            default  => [9.0, 9.0],
        };

        $tripsToUpdate = Trip::whereIn('id', $ids)->get();
        foreach ($tripsToUpdate as $trip) {
            $freight       = (float) $trip->freight_amount;
            $cgst          = round($freight * $cgstRate / 100, 2);
            $sgst          = round($freight * $sgstRate / 100, 2);
            $totalWithGst  = $freight + $cgst + $sgst;

            $trip->update([
                'invoice_no'     => $invoiceNo,
                'invoice_type'   => $invoiceType,
                'invoice_status' => 'invoiced',
                'invoiced_at'    => now(),
                'balance_amount' => $totalWithGst,
                'updated_by'     => auth()->id(),
            ]);
        }

        $trips = Trip::with(['vehicle', 'driver', 'party', 'supplier'])
            ->whereIn('id', $ids)
            ->orderBy('trip_date')
            ->get();
        $company = Company::where('is_deleted', false)->first();

        $invoiceTypeName = match ($invoiceType) {
            'rcm'    => 'RCM INVOICE',
            'exempt' => 'EXEMPTED INVOICE',
            default  => 'TAX INVOICE',
        };

        return view('Invoice.Trip_Invoice', compact('trips', 'company', 'invoiceNo', 'invoiceType', 'invoiceTypeName'));
    }

    /**
     * POST /invoice/{invoiceNo}/payment
     * Update payment status + collection date for all trips under this invoice.
     */
    public function updatePayment(Request $request, string $invoiceNo)
    {
        $validated = $request->validate([
            'payment_status'      => 'required|in:pending,partial,completed',
            'collected_amount'    => 'required|numeric|min:0',
            'collection_due_date' => 'nullable|date',
            'payment_mode'        => 'nullable|in:cash,upi,bank,cheque',
            'upi_details'         => 'nullable|string|max:255',
            'bank_details'        => 'nullable|string|max:255',
        ]);

        $trips = Trip::where('invoice_no', $invoiceNo)
            ->where('is_deleted', false)
            ->get();

        if ($trips->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Invoice not found.'], 404);
        }

        // Determine tax rates from invoice type
        $invoiceType = $trips->first()->invoice_type ?? 'normal';
        [$cgstRate, $sgstRate] = match ($invoiceType) {
            'rcm'    => [2.5, 2.5],
            'exempt' => [0.0, 0.0],
            default  => [9.0, 9.0],
        };

        // Calculate per-trip grand total (freight + tax) for proportional distribution
        $tripGrandTotals = $trips->mapWithKeys(function ($trip) use ($cgstRate, $sgstRate) {
            $freight = (float) $trip->freight_amount;
            $cgst    = round($freight * $cgstRate / 100, 2);
            $sgst    = round($freight * $sgstRate / 100, 2);
            return [$trip->id => $freight + $cgst + $sgst];
        });

        $totalGrand = $tripGrandTotals->sum();

        foreach ($trips as $trip) {
            $share = $totalGrand > 0
                ? round((float) $validated['collected_amount'] * $tripGrandTotals[$trip->id] / $totalGrand, 2)
                : 0;

            $trip->update([
                'payment_status'      => $validated['payment_status'],
                'collected_amount'    => $share,
                'collection_due_date' => $validated['collection_due_date'] ?? null,
                'payment_mode'        => $validated['payment_mode'] ?? null,
                'upi_details'         => $validated['upi_details'] ?? null,
                'bank_details'        => $validated['bank_details'] ?? null,
                'updated_by'          => auth()->id(),
            ]);
        }

        return response()->json([
            'success'             => true,
            'message'             => 'Payment updated successfully.',
            'payment_status'      => $validated['payment_status'],
            'collected_amount'    => (float) $validated['collected_amount'],
            'collection_due_date' => $validated['collection_due_date']
                ? \Carbon\Carbon::parse($validated['collection_due_date'])->format('d M Y')
                : null,
            'payment_mode'        => $validated['payment_mode'] ?? null,
            'upi_details'         => $validated['upi_details'] ?? null,
            'bank_details'        => $validated['bank_details'] ?? null,
        ]);
    }


    public function exportPdf(Request $request)
    {
        $ids         = $request->input('trip_ids', []);
        $invoiceType = $request->input('invoice_type', 'normal');

        $trips = Trip::with(['vehicle', 'driver', 'party', 'supplier'])
            ->whereIn('id', $ids)
            ->orderBy('trip_date')
            ->get();
        $company = Company::where('is_deleted', false)->first();

        $invoiceNo       = $trips->first()->invoice_no ?? 'DRAFT';
        $invoiceTypeName = match ($invoiceType) {
            'rcm'    => 'RCM INVOICE',
            'exempt' => 'EXEMPTED INVOICE',
            default  => 'TAX INVOICE',
        };

        return view('Invoice.Trip_Invoice_Pdf', compact('trips', 'company', 'invoiceNo', 'invoiceType', 'invoiceTypeName'));
    }

    /**
     * POST /invoice/excel  — CSV download
     */
    public function exportExcel(Request $request)
    {
        $ids         = $request->input('trip_ids', []);
        $invoiceType = $request->input('invoice_type', 'normal');

        $trips   = Trip::with(['vehicle', 'driver', 'party', 'supplier'])
            ->whereIn('id', $ids)
            ->orderBy('trip_date')
            ->get();
        $company = Company::where('is_deleted', false)->first();

        $invoiceNo = $trips->first()->invoice_no ?? 'DRAFT';
        $subTotal  = (float) $trips->sum('freight_amount');

        [$cgstRate, $sgstRate] = match ($invoiceType) {
            'rcm'    => [2.5, 2.5],
            'exempt' => [0.0, 0.0],
            default  => [9.0, 9.0],
        };
        $cgstAmt    = round($subTotal * $cgstRate / 100, 2);
        $sgstAmt    = round($subTotal * $sgstRate / 100, 2);
        $grandTotal = $subTotal + $cgstAmt + $sgstAmt;

        $rows   = [];
        $rows[] = ['Invoice No', $invoiceNo];
        $rows[] = ['Invoice Type', strtoupper($invoiceType)];
        $rows[] = ['Company', $company->company_name ?? ''];
        $rows[] = ['Date', now()->format('d/m/Y')];
        $rows[] = [];
        $rows[] = ['#', 'Description', 'HSN/SAC', 'Qty', 'Freight (₹)', 'Amount (₹)'];

        foreach ($trips as $i => $t) {
            $desc = '';
            if ($t->trip_date) $desc .= $t->trip_date->format('d/m/Y') . ' ';
            $desc .= strtoupper($t->from_location ?? '') . ' TO ' . strtoupper($t->to_location ?? '');
            if (!empty($t->lr_no))    $desc .= ' | DC: ' . $t->lr_no;
            if (!empty($t->material)) $desc .= ' | ' . $t->material;
            $rows[] = [$i + 1, trim($desc), '996511', '1.00', number_format($t->freight_amount, 2), number_format($t->freight_amount, 2)];
        }

        $rows[] = [];
        $rows[] = ['', '', '', '', 'Sub Total', number_format($subTotal, 2)];
        $rows[] = ['', '', '', '', "CGST ({$cgstRate}%)", number_format($cgstAmt, 2)];
        $rows[] = ['', '', '', '', "SGST ({$sgstRate}%)", number_format($sgstAmt, 2)];
        $rows[] = ['', '', '', '', 'Grand Total', number_format($grandTotal, 2)];

        $filename = 'Invoice_' . $invoiceNo . '_' . now()->format('Ymd') . '.csv';

        return response()->stream(function () use ($rows) {
            $h = fopen('php://output', 'w');
            foreach ($rows as $row) { fputcsv($h, $row); }
            fclose($h);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
