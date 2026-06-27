<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;


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
            'license_number' => ['required', 'string', 'max:16', Rule::unique('drivers', 'license_number')->where('is_deleted', 0)],
            'mobile'         => 'required|string|max:20',
            'aadhar_number'  => ['required', 'string', 'max:12', Rule::unique('drivers', 'aadhar_number')->where('is_deleted', 0)],
            'pan_number'     => ['required', 'string', 'max:10', Rule::unique('drivers', 'pan_number')->where('is_deleted', 0)],
            'dob'            => 'required|string|max:20',
            'state'          => 'nullable|string|max:100',
            'district'       => 'nullable|string|max:100',
            'city'           => 'nullable|string|max:100',
            'postal_code'    => 'nullable|string|max:6',
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

        // Handle photo uploads (direct file or temp upload from validation error)
        foreach (['driver_photo', 'aadhar_photo', 'pan_photo', 'license_photo'] as $photo) {
            if ($request->hasFile($photo)) {
                $validated[$photo] = $request->file($photo)
                    ->store('drivers/photos', 'public');
            } elseif ($tempPath = $request->input($photo . '_temp')) {
                $newPath = 'drivers/photos/' . basename($tempPath);
                Storage::disk('public')->move($tempPath, $newPath);
                $validated[$photo] = $newPath;
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
            'license_number' => ['required', 'string', 'max:16', Rule::unique('drivers', 'license_number')->ignore($id)->where('is_deleted', 0)],
            'mobile'         => 'required|string|max:20',
            'aadhar_number'  => ['required', 'string', 'max:12', Rule::unique('drivers', 'aadhar_number')->ignore($id)->where('is_deleted', 0)],
            'pan_number'     => ['required', 'string', 'max:10', Rule::unique('drivers', 'pan_number')->ignore($id)->where('is_deleted', 0)],
            'dob'            => 'required|string|max:20',
            'state'          => 'nullable|string|max:100',
            'district'       => 'nullable|string|max:100',
            'city'           => 'nullable|string|max:100',
            'postal_code'    => 'nullable|string|max:6',
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
            } elseif ($tempPath = $request->input($photo . '_temp')) {
                if ($driver->$photo && $driver->$photo !== $tempPath) {
                    Storage::disk('public')->delete($driver->$photo);
                }
                $newPath = 'drivers/photos/' . basename($tempPath);
                Storage::disk('public')->move($tempPath, $newPath);
                $validated[$photo] = $newPath;
            } else {
                unset($validated[$photo]);
            }
        }

        $driver->update($validated);

        return redirect()->route('driver.edit', $id)->with('success', 'Driver updated successfully');
    }

    /**
     * Upload a file temporarily (used to persist files across validation errors)
     */
    public function uploadTemp(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $path = $request->file('file')->store('temp/uploads', 'public');

        return response()->json([
            'path' => $path,
            'url'  => asset('storage/' . $path),
            'name' => $request->file('file')->getClientOriginalName(),
        ]);
    }

    /**
     * Soft-delete the specified driver
     */
    public function destroy($id)
    {
        $driver = Driver::findOrFail($id);
        $suffix = '-deleted-' . $driver->id;
        $driver->update([
            'is_deleted'      => true,
            'is_active'       => false,
            'license_number'  => $driver->license_number . $suffix,
            'aadhar_number'   => $driver->aadhar_number . $suffix,
            'pan_number'      => $driver->pan_number . $suffix,
        ]);

        return redirect()->route('driver')->with('success', 'Driver deleted successfully');
    }
}
