<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display list of all suppliers
     */
    public function index()
    {
        $suppliers = Supplier::where('is_active', true)
            ->where('is_deleted', false)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('Supplier_Master.Supplier', compact('suppliers'));
    }

    /**
     * Store a newly created supplier in database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|unique:suppliers,email',
            'mobile'  => 'required|string|max:20',
            'address' => 'nullable|string',
        ]);

        $validated['is_active']  = true;
        $validated['is_deleted'] = false;

        if (auth()->check()) {
            $validated['created_by'] = auth()->id();
        }

        $supplier = Supplier::create($validated);

        return response()->json(['message' => 'Supplier added successfully', 'supplier' => $supplier]);
    }

    /**
     * Show supplier details (JSON for modal)
     */
    public function view($id)
    {
        $supplier = Supplier::findOrFail($id);

        return response()->json($supplier);
    }

    /**
     * Return supplier data for editing (JSON for modal)
     */
    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);

        return response()->json($supplier);
    }

    /**
     * Update the specified supplier in database
     */
    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|unique:suppliers,email,' . $id,
            'mobile'  => 'required|string|max:20',
            'address' => 'nullable|string',
        ]);

        if (auth()->check()) {
            $validated['updated_by'] = auth()->id();
        }

        $supplier->update($validated);

        return response()->json(['message' => 'Supplier updated successfully', 'supplier' => $supplier]);
    }

    /**
     * Soft-delete the specified supplier
     */
    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->update(['is_deleted' => true, 'is_active' => false]);

        return redirect()->route('supplier')->with('success', 'Supplier deleted successfully');
    }
}
