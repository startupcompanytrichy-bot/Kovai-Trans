<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleDocument;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VehicleController extends Controller
{
    /**
     * Display list of all vehicles
     */
    public function index()
    {
        $vehicles  = Vehicle::with('supplier')->orderBy('created_at', 'desc')->get();
        $suppliers = Supplier::where('is_active', true)->where('is_deleted', false)->orderBy('name')->get();

        return view('Vehicle_Master.Vehicles_Table', compact('vehicles', 'suppliers'));
    }

    public function add()
    {
        $suppliers = Supplier::all(); // Assuming you have a Supplier model
        return view('Vehicle_Master.Vehicles_Table', compact('suppliers'));
    }

    /**
     * Store a newly created vehicle (AJAX — returns JSON)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_name'          => 'nullable|string|max:255',
            'vehicle_number'        => 'required|string|max:50|unique:vehicles,vehicle_number',
            'owner_type'            => 'nullable|string|max:50',
            'supplier_id'           => 'nullable|integer|exists:suppliers,id',
            'vehicle_type'          => 'nullable|string|max:50',
            'asset_make'            => 'nullable|string|max:100',
            'asset_type'            => 'nullable|string|max:100',
            'engine_number'         => 'nullable|string|max:100',
            'chassis_number'        => 'nullable|string|max:100',
            'rc_number'             => 'nullable|string|max:100',
            'insurance_expiry_date' => 'nullable|date',
            'fitness_expiry_date'   => 'nullable|date',
            'permit_expiry_date'    => 'nullable|date',
            'puc_expiry_date'       => 'nullable|date',
        ]);

        $validated['status'] = 'active';

        if (auth()->check()) {
            $validated['created_by'] = auth()->id();
        }

        Vehicle::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Vehicle added successfully']);
        }

        return redirect()->route('vehicle')->with('success', 'Vehicle added successfully');
    }

    /**
     * Return vehicle data as JSON for the slide-in view panel
     */
    public function data($id)
    {
        $vehicle = Vehicle::with(['supplier', 'documents'])->findOrFail($id);

        $typeLabels = ['lorry'=>'Lorry','truck'=>'Truck','trailer'=>'Trailer','mini_truck'=>'Mini Truck','container'=>'Container','tipper'=>'Tipper'];

        $expiryFields = ['insurance_expiry_date','fitness_expiry_date','permit_expiry_date','puc_expiry_date'];
        $expiryLabels = ['insurance_expiry_date'=>'Insurance','fitness_expiry_date'=>'Fitness','permit_expiry_date'=>'Permit','puc_expiry_date'=>'PUC'];
        $expiries = [];
        foreach ($expiryFields as $f) {
            $val  = $vehicle->$f;
            $stat = 'none';
            $fmt  = '—';
            if ($val) {
                $d    = \Carbon\Carbon::parse($val);
                $diff = $d->diffInDays(now(), false);
                $fmt  = $d->format('d M Y');
                $stat = $diff > 0 ? 'expired' : ($d->diffInDays(now()) <= 30 ? 'warn' : 'ok');
            }
            $expiries[] = ['label' => $expiryLabels[$f], 'value' => $fmt, 'status' => $stat];
        }

        $docs = [];
        foreach ($vehicle->documents as $doc) {
            $docs[] = [
                'label'     => VehicleDocument::$typeLabels[$doc->document_type] ?? $doc->document_type,
                'file_name' => $doc->file_name,
                'file_size' => $doc->file_size_human,
                'date'      => $doc->created_at->format('d M Y'),
                'url'       => asset('storage/'.$doc->file_path),
                'ext'       => $doc->file_extension,
            ];
        }

        return response()->json([
            'id'             => $vehicle->id,
            'vehicle_number' => $vehicle->vehicle_number,
            'vehicle_name'   => $vehicle->vehicle_name,
            'owner_type'     => $vehicle->owner_type,
            'vehicle_type'   => $vehicle->vehicle_type ? ($typeLabels[$vehicle->vehicle_type] ?? $vehicle->vehicle_type) : null,
            'asset_make'     => $vehicle->asset_make,
            'asset_type'     => $vehicle->asset_type,
            'supplier'       => optional($vehicle->supplier)->name,
            'engine_number'  => $vehicle->engine_number,
            'chassis_number' => $vehicle->chassis_number,
            'rc_number'      => $vehicle->rc_number,
            'permit_number'  => $vehicle->permit_number,
            'status'         => $vehicle->status ?? 'active',
            'created_at'     => $vehicle->created_at->format('d M Y'),
            'updated_at'     => $vehicle->updated_at->format('d M Y'),
            'expiries'       => $expiries,
            'documents'      => $docs,
            'edit_url'       => route('vehicle.edit', $vehicle->id),
        ]);
    }

    /**
     * Show the dedicated View page
     */
    public function view($id)
    {
        $vehicle   = Vehicle::with('documents')->findOrFail($id);
        $docTypes  = VehicleDocument::$typeLabels;
        $docIcons  = VehicleDocument::$typeIcons;

        // Index documents by type for easy access in blade
        $documents = $vehicle->documents->keyBy('document_type');

        return view('Vehicle_Master.View_Vehicle', compact('vehicle', 'docTypes', 'docIcons', 'documents'));
    }

    /**
     * Show the full Edit page
     */
    public function edit($id)
    {
        $vehicle   = Vehicle::with('documents')->findOrFail($id);
        $suppliers = Supplier::where('is_active', true)->where('is_deleted', false)->get();
        $docTypes  = VehicleDocument::$typeLabels;
        $docIcons  = VehicleDocument::$typeIcons;

        // Index documents by type for easy access in blade
        $documents = $vehicle->documents->keyBy('document_type');

        return view('Vehicle_Master.Edit_Vehicle', compact('vehicle', 'suppliers', 'docTypes', 'docIcons', 'documents'));
    }

    /**
     * Update vehicle details and handle document uploads
     */
    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $validated = $request->validate([
            'vehicle_name'          => 'nullable|string|max:255',
            'vehicle_number'        => 'required|string|max:50|unique:vehicles,vehicle_number,' . $id,
            'owner_type'            => 'nullable|string|max:50',
            'supplier_id'           => 'nullable|integer|exists:suppliers,id',
            'vehicle_type'          => 'nullable|string|max:50',
            'asset_make'            => 'nullable|string|max:100',
            'asset_type'            => 'nullable|string|max:100',
            'engine_number'         => 'nullable|string|max:100',
            'chassis_number'        => 'nullable|string|max:100',
            'rc_number'             => 'nullable|string|max:100',
            'insurance_expiry_date' => 'nullable|date',
            'fitness_expiry_date'   => 'nullable|date',
            'permit_expiry_date'    => 'nullable|date',
            'puc_expiry_date'       => 'nullable|date',

        ]);

        // Document file validation
        $docTypes = array_keys(VehicleDocument::$typeLabels);
        foreach ($docTypes as $type) {
            $request->validate([
                "doc_{$type}" => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            ]);
        }

        if (auth()->check()) {
            $validated['updated_by'] = auth()->id();
        }

        $vehicle->update($validated);

        // Handle document uploads
        foreach ($docTypes as $type) {
            $fileKey = "doc_{$type}";
            if ($request->hasFile($fileKey)) {
                $file      = $request->file($fileKey);
                $ext       = $file->getClientOriginalExtension();
                $fileName  = time() . '_' . $id . '_' . $type . '.' . $ext;
                $path      = $file->storeAs('vehicles/docs', $fileName, 'public');

                // Delete old document of same type
                $existing = VehicleDocument::where('vehicle_id', $id)
                    ->where('document_type', $type)
                    ->first();

                if ($existing) {
                    Storage::disk('public')->delete($existing->file_path);
                    $existing->delete();
                }

                // Save new document record
                VehicleDocument::create([
                    'vehicle_id'     => $id,
                    'document_type'  => $type,
                    'document_label' => VehicleDocument::$typeLabels[$type],
                    'file_name'      => $file->getClientOriginalName(),
                    'file_path'      => $path,
                    'file_extension' => $ext,
                    'file_size'      => $file->getSize(),
                    'uploaded_by'    => auth()->check() ? auth()->id() : null,
                ]);
            }

            // Handle delete checkbox
            if ($request->has("delete_doc_{$type}")) {
                $existing = VehicleDocument::where('vehicle_id', $id)
                    ->where('document_type', $type)
                    ->first();
                if ($existing) {
                    Storage::disk('public')->delete($existing->file_path);
                    $existing->delete();
                }
            }
        }

        return redirect()->route('vehicle.edit', $id)
            ->with('success', 'Vehicle updated successfully!');
    }

    /**
     * Delete the specified vehicle and all its documents
     */
    public function destroy($id)
    {
        $vehicle   = Vehicle::findOrFail($id);
        $documents = VehicleDocument::where('vehicle_id', $id)->get();

        foreach ($documents as $doc) {
            Storage::disk('public')->delete($doc->file_path);
            $doc->delete();
        }

        $vehicle->delete();

        return redirect()->route('vehicle')->with('success', 'Vehicle deleted successfully');
    }
}
