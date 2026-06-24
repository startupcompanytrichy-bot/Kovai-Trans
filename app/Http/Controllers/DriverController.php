<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DriverController extends Controller
{
    /**
     * Display list of all drivers
     */
    public function index()
    {
        $drivers = Driver::where('is_active', true)
            ->where('is_deleted', false)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('Drivers_Master.Drivers_Table', compact('drivers'));
    }

    /**
     * Show the Add Driver page
     */
    public function create()
    {
        return view('Drivers_Master.New_Driver');
    }

    /**
     * Store a newly created driver
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'driver_type'    => 'nullable|in:own,rental',
            'license_number' => 'required|string|max:100|unique:drivers,license_number',
            'mobile'         => 'required|string|max:20',
            'aadhar_number'  => 'required|string|max:20|unique:drivers,aadhar_number',
            'pan_number'     => 'required|string|max:10|unique:drivers,pan_number',
            'dob'            => 'required|string|max:20',
            'state'          => 'nullable|string|max:100',
            'district'       => 'nullable|string|max:100',
            'city'           => 'nullable|string|max:100',
            'postal_code'    => 'nullable|string|max:10',
            'address'        => 'nullable|string',
            'remarks'        => 'nullable|string',
            'driver_photo'   => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'aadhar_photo'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'pan_photo'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'license_photo'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);
        $validated['driver_type'] = $validated['driver_type'] ?? 'own';

        $validated['is_active']  = true;
        $validated['is_deleted'] = false;

        if (auth()->check()) {
            $validated['created_by'] = auth()->id();
        }

        // Handle photo uploads
        foreach (['driver_photo', 'aadhar_photo', 'pan_photo', 'license_photo'] as $photo) {
            if ($request->hasFile($photo)) {
                $validated[$photo] = $request->file($photo)
                    ->store('drivers/photos', 'public');
            } else {
                unset($validated[$photo]);
            }
        }

        Driver::create($validated);

        return redirect()->route('driver')->with('success', 'Driver added successfully');
    }

    /**
     * Return driver data as JSON for the view modal
     */
    public function view($id)
    {
        $driver = Driver::findOrFail($id);

        // Append public URLs for photos
        $data = $driver->toArray();
        foreach (['driver_photo', 'aadhar_photo', 'pan_photo', 'license_photo'] as $photo) {
            $data[$photo . '_url'] = $driver->$photo
                ? asset('storage/' . $driver->$photo)
                : null;
        }

        return response()->json($data);
    }

    /**
     * Show the Edit Driver page
     */
    public function edit($id)
    {
        $driver = Driver::findOrFail($id);

        return view('Drivers_Master.Edit_Driver', compact('driver'));
    }

    /**
     * Update the specified driver
     */
    public function update(Request $request, $id)
    {
        $driver = Driver::findOrFail($id);

        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'driver_type'    => 'nullable|in:own,rental',
            'license_number' => 'required|string|max:100|unique:drivers,license_number,' . $id,
            'mobile'         => 'required|string|max:20',
            'aadhar_number'  => 'required|string|max:20|unique:drivers,aadhar_number,' . $id,
            'pan_number'     => 'required|string|max:10|unique:drivers,pan_number,' . $id,
            'dob'            => 'required|string|max:20',
            'state'          => 'nullable|string|max:100',
            'district'       => 'nullable|string|max:100',
            'city'           => 'nullable|string|max:100',
            'postal_code'    => 'nullable|string|max:10',
            'address'        => 'nullable|string',
            'remarks'        => 'nullable|string',
            'driver_photo'   => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'aadhar_photo'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'pan_photo'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'license_photo'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);
        $validated['driver_type'] = $validated['driver_type'] ?? 'own';

        if (auth()->check()) {
            $validated['updated_by'] = auth()->id();
        }

        // Handle photo uploads — delete old file and store new one
        foreach (['driver_photo', 'aadhar_photo', 'pan_photo', 'license_photo'] as $photo) {
            if ($request->hasFile($photo)) {
                if ($driver->$photo) {
                    Storage::disk('public')->delete($driver->$photo);
                }
                $validated[$photo] = $request->file($photo)
                    ->store('drivers/photos', 'public');
            } else {
                unset($validated[$photo]);
            }
        }

        $driver->update($validated);

        return redirect()->route('driver.edit', $id)->with('success', 'Driver updated successfully');
    }

    /**
     * Soft-delete the specified driver
     */
    public function destroy($id)
    {
        $driver = Driver::findOrFail($id);
        $driver->update(['is_deleted' => true, 'is_active' => false]);

        return redirect()->route('driver')->with('success', 'Driver deleted successfully');
    }
}
