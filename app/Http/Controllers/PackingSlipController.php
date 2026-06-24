<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\PackCustomer;
use App\Models\PackQuality;
use App\Models\PackSlip;
use App\Models\PackSlipBaleItem;
use App\Models\Party;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PackingSlipController extends Controller
{
    public function dashboard()
    {
        $company = Company::where('is_deleted', false)->first();

        $base = Trip::where('is_deleted', false)->whereNotNull('lr_no');

        $totalTrips   = (clone $base)->count();
        $totalQty     = (clone $base)->sum('quantity');
        $totalParties = (clone $base)->distinct('party_id')->count('party_id');
        $totalVehicles = (clone $base)->whereNotNull('vehicle_id')->distinct('vehicle_id')->count('vehicle_id');
        $invoiced     = (clone $base)->where('invoice_status', 'invoiced')->count();
        $pending      = (clone $base)->where(function ($q) { $q->whereNull('invoice_status')->orWhere('invoice_status', '!=', 'invoiced'); })->count();

        $monthly = (clone $base)
            ->select(DB::raw("TO_CHAR(trip_date, 'YYYY-MM') as ym"), DB::raw('COUNT(*) as cnt'), DB::raw('SUM(quantity) as qty'))
            ->where('trip_date', '>=', now()->subMonths(6)->startOfMonth())
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        $recent = (clone $base)->with(['party', 'vehicle'])
            ->orderBy('trip_date', 'desc')
            ->limit(12)
            ->get();

        $topParties = (clone $base)
            ->select('party_id', DB::raw('COUNT(*) as cnt'), DB::raw('SUM(quantity) as qty'))
            ->whereNotNull('party_id')
            ->groupBy('party_id')
            ->orderByDesc('cnt')
            ->limit(5)
            ->get();
        $partyNames = Party::whereIn('id', $topParties->pluck('party_id'))->get()->keyBy('id');

        return view('PackingSlip.dashboard', compact(
            'company', 'totalTrips', 'totalQty', 'totalParties', 'totalVehicles',
            'invoiced', 'pending', 'monthly', 'recent', 'topParties', 'partyNames'
        ));
    }

    public function customers()
    {
        $company = Company::where('is_deleted', false)->first();
        $customers = PackCustomer::orderBy('name')->get();

        return view('PackingSlip.customers', compact('customers', 'company'));
    }

    public function storeCustomer(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:255',
            'gstin'   => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'notes'   => 'nullable|string|max:1000',
        ]);

        $data['created_by'] = session('user_id');

        PackCustomer::create($data);

        return redirect()->route('packing-slip.customers')->with('success', 'Customer added successfully.');
    }

    public function editCustomer($id)
    {
        return response()->json(PackCustomer::findOrFail($id));
    }

    public function updateCustomer(Request $request, $id)
    {
        $customer = PackCustomer::findOrFail($id);

        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:255',
            'gstin'   => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'notes'   => 'nullable|string|max:1000',
        ]);

        $customer->update($data);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Customer updated successfully']);
        }

        return redirect()->route('packing-slip.customers')->with('success', 'Customer updated successfully.');
    }

    public function destroyCustomer($id)
    {
        PackCustomer::findOrFail($id)->delete();

        return redirect()->route('packing-slip.customers')->with('success', 'Customer deleted successfully.');
    }

    public function slipIndex()
    {
        $company = Company::where('is_deleted', false)->first();
        $slips = PackSlip::with(['customer', 'baleItems'])->orderBy('id', 'desc')->get();

        return view('PackingSlip.index', compact('slips', 'company'));
    }

    public function showSlip($id)
    {
        $slip = PackSlip::with(['customer', 'baleItems'])->findOrFail($id);
        $company = Company::where('is_deleted', false)->first();

        return view('PackingSlip.show', compact('slip', 'company'));
    }

    public function createSlip()
    {
        $company    = Company::where('is_deleted', false)->first();
        $customers  = PackCustomer::orderBy('name')->get();
        $qualities  = PackQuality::orderBy('name')->get();
        $nextBaleNo = (PackSlipBaleItem::max('bale_no') ?? 0) ?: 1;

        return view('PackingSlip.create', compact('company', 'customers', 'qualities', 'nextBaleNo'));
    }

    public function editSlip($id)
    {
        $slip = PackSlip::with('baleItems')->findOrFail($id);
        $company    = Company::where('is_deleted', false)->first();
        $customers  = PackCustomer::orderBy('name')->get();
        $qualities  = PackQuality::orderBy('name')->get();
        $nextBaleNo = PackSlipBaleItem::max('bale_no') ?? 0;

        return view('PackingSlip.create', compact('company', 'customers', 'qualities', 'nextBaleNo', 'slip'));
    }

    public function storeSlip(Request $request)
    {
        $data = $request->validate([
            'id'               => 'nullable|integer|exists:pack_slips,id',
            'lr_no'            => 'nullable|string|max:100',
            'bill_no'          => 'nullable|string|max:100',
            'lot_no'           => 'nullable|string|max:100',
            'slip_date'        => 'required|date',
            'pack_customer_id' => 'required|exists:pack_customers,id',
            'quality'          => 'nullable|string|max:255',
            'notes'            => 'nullable|string|max:1000',
            'items'            => 'nullable|array',
            'items.*.bale_no'  => 'required|integer|min:1',
            'items.*.s_no'     => 'required|integer|min:1',
            'items.*.meter'    => 'nullable|numeric|min:0',
            'items.*.weight'   => 'nullable|numeric|min:0',
        ]);

        $data['created_by'] = session('user_id');

        $items = collect($request->input('items', []))->filter(fn($i) => ($i['meter'] ?? 0) > 0)->values();
        $items = $items->map(fn($i) => array_merge($i, ['meter' => number_format((float)$i['meter'], 2, '.', '')]));
        $data['no_of_bale'] = $items->count();
        $data['total_meter'] = $items->sum('meter');
        $data['fin_year']   = \currentFY()?->id;

        if ($request->filled('id')) {
            $slip = PackSlip::findOrFail($request->id);
            $slip->update($data);

            $incomingKeys = $items->map(fn($i) => $i['bale_no'].'-'.$i['s_no'])->toArray();

            foreach ($slip->baleItems as $existing) {
                $key = $existing->bale_no.'-'.$existing->s_no;
                if (!in_array($key, $incomingKeys)) {
                    $existing->delete();
                }
            }

            foreach ($items as $item) {
                $key = $item['bale_no'].'-'.$item['s_no'];
                $existing = $slip->baleItems()->where('bale_no', $item['bale_no'])->where('s_no', $item['s_no'])->first();
                if ($existing) {
                    $existing->update([
                        'meter'  => $item['meter'] ?? 0,
                        'weight' => $item['weight'] ?? 0,
                    ]);
                } else {
                    $slip->baleItems()->create([
                        'bale_no' => $item['bale_no'],
                        's_no'    => $item['s_no'],
                        'meter'   => $item['meter'] ?? 0,
                        'weight'  => $item['weight'] ?? 0,
                    ]);
                }
            }
        } else {
            $slip = PackSlip::create($data);
            foreach ($items as $item) {
                $slip->baleItems()->create([
                    'bale_no' => $item['bale_no'],
                    's_no'    => $item['s_no'],
                    'meter'   => $item['meter'] ?? 0,
                    'weight'  => $item['weight'] ?? 0,
                ]);
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Packing slip saved successfully.',
                'id'      => $slip->id,
            ]);
        }

        return redirect()->route('packing-slip.index')->with('success', 'Packing slip created successfully.');
    }

    public function getNextBaleNo()
    {
        $last = PackSlipBaleItem::max('bale_no') ?? 0;
        return response()->json(['next_bale_no' => $last ?: 1]);
    }

    public function printSlip($id)
    {
        $slip = PackSlip::with(['customer', 'baleItems'])->findOrFail($id);
        $company = Company::where('is_deleted', false)->first();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('PackingSlip.pdf', compact('slip', 'company'));
        return $pdf->download('packing-slip-' . ($slip->bill_no ?? $slip->id) . '.pdf');
    }

    public function storeQuality(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $exists = PackQuality::where('name', $data['name'])->exists();
        if ($exists) {
            return redirect()->back()->with('error', 'Quality "' . $data['name'] . '" already exists.');
        }

        PackQuality::create($data);

        return redirect()->back()->with('success', 'Quality added successfully.');
    }

    public function editQuality($id)
    {
        return response()->json(PackQuality::findOrFail($id));
    }

    public function updateQuality(Request $request, $id)
    {
        $quality = PackQuality::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $exists = PackQuality::where('name', $data['name'])->where('id', '!=', $id)->exists();
        if ($exists) {
            return redirect()->back()->with('error', 'Quality "' . $data['name'] . '" already exists.');
        }

        $quality->update($data);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Quality updated successfully']);
        }

        return redirect()->back()->with('success', 'Quality updated successfully.');
    }

    public function destroyQuality($id)
    {
        PackQuality::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Quality deleted successfully.');
    }

    public function qualities()
    {
        $company   = Company::where('is_deleted', false)->first();
        $qualities = PackQuality::orderBy('name')->get();

        return view('PackingSlip.qualities', compact('company', 'qualities'));
    }

    public function index(Request $request)
    {
        $company = Company::where('is_deleted', false)->first();

        $query = Trip::with(['party', 'vehicle'])
            ->where('is_deleted', false)
            ->whereNotNull('lr_no');

        if ($request->filled('party_id')) {
            $query->where('party_id', $request->party_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('trip_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('trip_date', '<=', $request->date_to);
        }

        $trips = $query->orderBy('trip_date', 'desc')->get();

        $totalQty   = $trips->sum('quantity');
        $totalTrips = $trips->count();

        $parties = Party::orderBy('company_name')->orderBy('name')->get();

        return view('Reports.Packing_Slip_Ledger', compact('trips', 'totalQty', 'totalTrips', 'parties', 'company'));
    }
}
