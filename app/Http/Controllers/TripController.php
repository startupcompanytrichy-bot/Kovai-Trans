<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Party;
use App\Models\Supplier;
use App\Models\Trip;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function index()
    {
        $query = Trip::with(['vehicle', 'driver', 'party'])
            ->where('is_deleted', false);

        \applyFinYearFilter($query);

        $trips = $query->orderBy('created_at', 'desc')->get();

        // Dashboard statistics
        $stats = [
            'total'       => $trips->count(),
            'active'      => $trips->whereIn('status', ['planned', 'running'])->count(),
            'running'     => $trips->where('status', 'running')->count(),
            'completed'   => $trips->where('status', 'completed')->count(),
            'cancelled'   => $trips->where('status', 'cancelled')->count(),
            'profit'      => $trips->filter(fn($t) => $t->is_profitable)->count(),
            'loss'        => $trips->filter(fn($t) => !$t->is_profitable && $t->status === 'completed')->count(),
            'pending_col' => $trips->where('payment_status', 'pending')->count(),
            'total_freight'   => $trips->sum('freight_amount'),
            'total_collected' => $trips->sum('collected_amount'),
            'total_outstanding' => $trips->sum(fn($t) => $t->outstanding_amount),
        ];

        return view('Trips.Trip_Table', compact('trips', 'stats'));
    }

    public function create()
    {
        return view('Trips.New_Trip', $this->formData());
    }

    public function store(Request $request)
    {
        $validated = $this->validateTrip($request);
        $validated['trip_no']        = $this->generateTripNo();
        $validated['balance_amount'] = $this->calcBalance($validated);
        $validated['status']         = $validated['status'] ?? 'planned';
        $validated['workflow_status'] = $validated['workflow_status'] ?? 'pending';
        $validated['fin_year']       = \currentFY()?->id;
        $validated['is_active']      = true;
        $validated['is_deleted']     = false;

        // Auto-generate LR No if not provided
        if (empty($validated['lr_no'])) {
            $year  = now()->year;
            $count = Trip::whereYear('created_at', $year)->count() + 1;
            $validated['lr_no'] = 'LRN' . $year . str_pad((string) $count, 3, '0', STR_PAD_LEFT);
        }

        if (auth()->check()) {
            $validated['created_by'] = auth()->id();
        }

        // Handle document upload
        if ($request->hasFile('document_file')) {
            $validated['document_path'] = $request->file('document_file')
                ->store('trips/documents', 'public');
        }

        Trip::create($validated);

        return redirect()->route('trip')->with('success', 'Trip created successfully');
    }

    public function view($id)
    {
        $trip = Trip::with(['vehicle', 'driver', 'party', 'supplier'])->findOrFail($id);

        return response()->json([
            'id'                     => $trip->id,
            'trip_no'                => $trip->trip_no,
            'booking_date'           => optional($trip->booking_date)->format('d-m-Y'),
            'trip_date'              => optional($trip->trip_date)->format('d-m-Y'),
            'expected_delivery_date' => optional($trip->expected_delivery_date)->format('d-m-Y'),
            'vehicle'                => optional($trip->vehicle)->vehicle_number,
            'driver'                 => optional($trip->driver)->name,
            'driver_mobile'          => optional($trip->driver)->mobile,
            'party'                  => optional($trip->party)->company_name ?: optional($trip->party)->name,
            'party_mobile'           => optional($trip->party)->phone,
            'supplier'               => optional($trip->supplier)->name,
            'from_location'          => $trip->from_location,
            'to_location'            => $trip->to_location,
            'distance_km'            => $trip->distance_km,
            'material'               => $trip->material,
            'load_type'              => $trip->load_type,
            'quantity'               => $trip->quantity,
            'billing_type'           => $trip->billing_type ? ucwords(str_replace('_', ' ', $trip->billing_type)) : null,
            'freight_amount'         => $trip->freight_amount,
            'advance_amount'         => $trip->advance_amount,
            'diesel_advance'         => $trip->diesel_advance,
            'driver_bata'            => $trip->driver_bata,
            'toll_charges'           => $trip->toll_charges,
            'loading_charges'        => $trip->loading_charges,
            'unloading_charges'      => $trip->unloading_charges,
            'other_expenses'         => $trip->other_expenses,
            'expense_notes'          => $trip->expense_notes,
            'total_expenses'         => $trip->total_expenses,
            'net_profit'             => $trip->net_profit,
            'balance_amount'         => $trip->balance_amount,
            'payment_status'         => $trip->payment_status,
            'collected_amount'       => $trip->collected_amount,
            'outstanding_amount'     => $trip->outstanding_amount,
            'collection_due_date'    => optional($trip->collection_due_date)->format('d-m-Y'),
            'payment_mode'           => $trip->payment_mode,
            'loading_date'           => optional($trip->loading_date)->format('d-m-Y'),
            'unloading_date'         => optional($trip->unloading_date)->format('d-m-Y'),
            'start_kms_reading'      => $trip->start_kms_reading,
            'lr_no'                  => $trip->lr_no,
            'status'                 => ucfirst($trip->status),
            'workflow_status'        => $trip->workflow_status,
            'remarks'                => $trip->remarks,
        ]);
    }

    public function edit($id)
    {
        $trip = Trip::with(['vehicle', 'driver', 'party', 'supplier', 'payments'])->findOrFail($id);

        // Sync collected_amount + payment_status from actual payments table
        $totalCollected = $trip->payments->sum('amount');
        $paymentStatus  = 'pending';
        if ($totalCollected >= (float) $trip->freight_amount && $totalCollected > 0) {
            $paymentStatus = 'completed';
        } elseif ($totalCollected > 0) {
            $paymentStatus = 'partial';
        }

        if (
            (float) $trip->collected_amount !== (float) $totalCollected ||
            $trip->payment_status !== $paymentStatus
        ) {
            $trip->update([
                'collected_amount' => $totalCollected,
                'payment_status'   => $paymentStatus,
            ]);
            $trip->collected_amount = $totalCollected;
            $trip->payment_status   = $paymentStatus;
        }

        return view('Trips.Edit_Trip', array_merge($this->formData(), compact('trip')));
    }

    public function update(Request $request, $id)
    {
        $trip = Trip::findOrFail($id);

        $validated = $this->validateTrip($request, $id);
        $validated['balance_amount'] = $this->calcBalance($validated);

        if (auth()->check()) {
            $validated['updated_by'] = auth()->id();
        }

        $trip->update($validated);

        return redirect()->route('trip.edit', $id)->with('success', 'Trip updated successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $trip = Trip::findOrFail($id);

        $request->validate([
            'status' => 'required|string|in:planned,running,completed,cancelled',
        ]);

        $trip->update([
            'status'     => $request->status,
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'status'  => $trip->status,
            'message' => 'Status updated to ' . ucfirst($trip->status),
        ]);
    }

    public function destroy($id)
    {
        $trip = Trip::findOrFail($id);
        $trip->update(['is_deleted' => true, 'is_active' => false]);

        return redirect()->route('trip')->with('success', 'Trip deleted successfully');
    }

    public function updatePayment(Request $request, $id)
    {
        $trip = Trip::findOrFail($id);

        $validated = $request->validate([
            'payment_status'       => 'required|string|in:pending,partial,completed',
            'collected_amount'     => 'required|numeric|min:0',
            'collection_due_date'  => 'nullable|date',
            'payment_mode'         => 'nullable|string|in:cash,upi,bank,cheque',
            'upi_details'          => 'nullable|string|max:255',
            'bank_details'         => 'nullable|string|max:255',
        ]);

        $trip->update([
            'payment_status'       => $validated['payment_status'],
            'collected_amount'     => $validated['collected_amount'],
            'collection_due_date'  => $validated['collection_due_date'] ?? null,
            'payment_mode'         => $validated['payment_mode'] ?? null,
            'upi_details'          => $validated['upi_details'] ?? null,
            'bank_details'         => $validated['bank_details'] ?? null,
            'updated_by'           => auth()->id(),
        ]);

        // Handle AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment details updated successfully',
                'data' => [
                    'payment_status'      => $trip->payment_status,
                    'collected_amount'    => $trip->collected_amount,
                    'collection_due_date' => $trip->collection_due_date,
                    'payment_mode'        => $trip->payment_mode,
                ]
            ]);
        }

        return redirect()->route('trip.edit', $id)->with('success', 'Payment details updated successfully');
    }

    // ── Private helpers ────────────────────────────────────────────────────────

    private function formData(): array
    {
        // Get vehicles/drivers currently on active trips (planned or running)
        $activeTrips = Trip::whereIn('status', ['planned', 'running'])
            ->where('is_deleted', false);

        \applyFinYearFilter($activeTrips);

        $busyVehicles = (clone $activeTrips)
            ->whereNotNull('vehicle_id')
            ->pluck('status', 'vehicle_id')
            ->toArray(); // [vehicle_id => trip_status]

        $busyDrivers = (clone $activeTrips)
            ->whereNotNull('driver_id')
            ->pluck('status', 'driver_id')
            ->toArray(); // [driver_id => trip_status]

        $vehicles = Vehicle::orderBy('vehicle_number')->get()->map(function ($v) use ($busyVehicles) {
            $v->trip_status = $busyVehicles[$v->id] ?? null; // null = free
            return $v;
        });

        $drivers = Driver::where('is_active', true)->where('is_deleted', false)->orderBy('name')->get()->map(function ($d) use ($busyDrivers) {
            $d->trip_status = $busyDrivers[$d->id] ?? null; // null = free
            return $d;
        });

        return [
            'vehicles'  => $vehicles,
            'parties'   => Party::orderBy('company_name')->orderBy('name')->get(),
            'drivers'   => $drivers,
            'suppliers' => Supplier::where('is_active', true)->where('is_deleted', false)->orderBy('name')->get(),
        ];
    }

    private function validateTrip(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'trip_no'                => 'nullable|string|max:100|unique:trips,trip_no' . ($id ? ',' . $id : ''),
            'booking_date'           => 'nullable|date',
            'trip_date'              => 'required|date',
            'expected_delivery_date' => 'nullable|date',
            'vehicle_id'             => 'required|exists:vehicles,id',
            'driver_id'              => 'nullable|exists:drivers,id',
            'supplier_id'            => 'nullable|exists:suppliers,id',
            'party_id'               => 'required|exists:parties,id',
            'from_location'          => 'required|string|max:255',
            'from_state'             => 'nullable|string|max:100',
            'from_district'          => 'nullable|string|max:100',
            'to_location'            => 'required|string|max:255',
            'to_state'               => 'nullable|string|max:100',
            'to_district'            => 'nullable|string|max:100',
            'distance_km'            => 'nullable|numeric|min:0',
            'distance_km'            => 'nullable|numeric|min:0',
            'material'               => 'nullable|string|max:255',
            'load_type'              => 'nullable|string|max:100',
            'quantity'               => 'nullable|numeric|min:0',
            'billing_type'           => 'required|string|in:fixed,per_tonne,per_kg,per_km,per_trip,per_day,per_hour,per_litre,per_bag',
            'freight_amount'         => 'required|numeric|min:0',
            'advance_amount'         => 'nullable|numeric|min:0',
            'diesel_advance'         => 'nullable|numeric|min:0',
            'driver_bata'            => 'nullable|numeric|min:0',
            'toll_charges'           => 'nullable|numeric|min:0',
            'loading_charges'        => 'nullable|numeric|min:0',
            'unloading_charges'      => 'nullable|numeric|min:0',
            'other_expenses'         => 'nullable|numeric|min:0',
            'expense_notes'          => 'nullable|string',
            'payment_status'         => 'nullable|string|in:pending,partial,completed',
            'collected_amount'       => 'nullable|numeric|min:0',
            'collection_due_date'    => 'nullable|date',
            'payment_mode'           => 'nullable|string|in:cash,upi,bank,cheque',
            'upi_details'            => 'nullable|string|max:255',
            'bank_details'           => 'nullable|string|max:255',
            'loading_date'           => 'nullable|date',
            'unloading_date'         => 'nullable|date|after_or_equal:loading_date',
            'start_kms_reading'      => 'nullable|numeric|min:0',
            'lr_no'                  => 'nullable|string|max:100',
            'document_number'        => 'nullable|string|max:255',
            'document_file'          => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
            'status'                 => 'nullable|string|in:planned,running,completed,cancelled',
            'workflow_status'        => 'nullable|string|in:pending,allocated,started,loading,in_transit,reached,unloading,completed,closed',
            'remarks'                => 'nullable|string',
        ]);
    }

    private function calcBalance(array $data): float
    {
        return (float) ($data['freight_amount'] ?? 0) - (float) ($data['advance_amount'] ?? 0);
    }

    private function generateTripNo(): string
    {
        $nextId = (int) Trip::max('id') + 1;

        return 'TRIP-' . now()->format('Ymd') . '-' . str_pad((string) $nextId, 4, '0', STR_PAD_LEFT);
    }

    public function generateLrNo(): \Illuminate\Http\JsonResponse
    {
        $year  = now()->year;
        $count = Trip::whereYear('created_at', $year)->count() + 1;
        $lrNo  = 'LRN' . $year . str_pad((string) $count, 3, '0', STR_PAD_LEFT);
        return response()->json(['lr_no' => $lrNo]);
    }

    /**
     * POST /trip/{id}/payments  — add a payment entry
     * Adds the payment to trip_payments, recalculates collected_amount on the trip.
     */
    public function addPayment(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $trip = Trip::findOrFail($id);

        $validated = $request->validate([
            'amount'       => 'required|numeric|min:0.01',
            'payment_mode' => 'nullable|string|in:cash,upi,bank,cheque',
            'reference'    => 'nullable|string|max:255',
            'note'         => 'nullable|string|max:500',
            'paid_on'      => 'nullable|date',
        ]);

        $payment = \App\Models\TripPayment::create([
            'fin_year'     => \currentFY()?->id,
            'trip_id'      => $trip->id,
            'amount'       => $validated['amount'],
            'payment_mode' => $validated['payment_mode'] ?? null,
            'reference'    => $validated['reference'] ?? null,
            'note'         => $validated['note'] ?? null,
            'paid_on'      => $validated['paid_on'] ?? now()->toDateString(),
            'created_by'   => auth()->id(),
        ]);

        // Recalculate total collected from all payments
        $totalCollected = \App\Models\TripPayment::where('trip_id', $trip->id)->sum('amount');

        $paymentStatus = 'pending';
        if ($totalCollected >= (float) $trip->freight_amount) {
            $paymentStatus = 'completed';
        } elseif ($totalCollected > 0) {
            $paymentStatus = 'partial';
        }

        $trip->update([
            'collected_amount' => $totalCollected,
            'payment_status'   => $paymentStatus,
            'updated_by'       => auth()->id(),
        ]);

        return response()->json([
            'success'         => true,
            'payment'         => [
                'id'           => $payment->id,
                'amount'       => (float) $payment->amount,
                'amount_fmt'   => number_format((float) $payment->amount, 0),
                'payment_mode' => $payment->payment_mode,
                'reference'    => $payment->reference,
                'note'         => $payment->note,
                'paid_on'      => $payment->paid_on->format('d M Y'),
            ],
            'total_collected' => (float) $totalCollected,
            'pending'         => (float) max(0, (float) $trip->freight_amount - $totalCollected),
            'payment_status'  => $paymentStatus,
            'freight'         => (float) $trip->freight_amount,
        ]);
    }

    /**
     * DELETE /trip/{id}/payments/{paymentId}  — remove a payment entry
     */
    public function deletePayment($id, $paymentId): \Illuminate\Http\JsonResponse
    {
        $trip    = Trip::findOrFail($id);
        $payment = \App\Models\TripPayment::where('trip_id', $trip->id)->findOrFail($paymentId);
        $payment->delete();

        $totalCollected = \App\Models\TripPayment::where('trip_id', $trip->id)->sum('amount');

        $paymentStatus = 'pending';
        if ($totalCollected >= (float) $trip->freight_amount) {
            $paymentStatus = 'completed';
        } elseif ($totalCollected > 0) {
            $paymentStatus = 'partial';
        }

        $trip->update([
            'collected_amount' => $totalCollected,
            'payment_status'   => $paymentStatus,
            'updated_by'       => auth()->id(),
        ]);

        return response()->json([
            'success'         => true,
            'total_collected' => $totalCollected,
            'pending'         => max(0, (float) $trip->freight_amount - $totalCollected),
            'payment_status'  => $paymentStatus,
        ]);
    }
}
