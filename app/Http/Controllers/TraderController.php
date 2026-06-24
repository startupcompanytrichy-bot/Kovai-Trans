<?php

namespace App\Http\Controllers;

use App\Models\Trader;
use Illuminate\Http\Request;

class TraderController extends Controller
{
    /**
     * Display list of all traders
     */
    public function index()
    {
        $traders = Trader::where('is_active', true)
            ->where('is_deleted', false);

        \applyFinYearFilter($traders);

        $traders = $traders->orderBy('created_at', 'desc')->get();

        // Build category map for the form dropdown (DB + static fallback)
        try {
            $categories = \App\Models\ExpenseCategory::where('is_active', true)
                ->orderBy('label')->get(['key', 'label', 'icon', 'color', 'bg'])
                ->keyBy('key')->toArray();
            if (empty($categories)) {
                $categories = \App\Models\Expense::$categories;
            }
        } catch (\Exception $e) {
            $categories = \App\Models\Expense::$categories;
        }

        return view('Trader_Master.Trader', compact('traders', 'categories'));
    }

    /**
     * Store a newly created trader in database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string',
            'category' => 'nullable|string|max:50',
        ]);

        $validated['fin_year']   = \currentFY()?->id;
        $validated['is_active']  = true;
        $validated['is_deleted'] = false;

        // Treat empty string as null (global trader)
        if (isset($validated['category']) && $validated['category'] === '') {
            $validated['category'] = null;
        }

        if (auth()->check()) {
            $validated['created_by'] = auth()->id();
        }

        $trader = Trader::create($validated);

        return response()->json(['message' => 'Trader added successfully', 'trader' => $trader]);
    }

    /**
     * Show trader details (JSON for modal)
     */
    public function view($id)
    {
        $trader = Trader::findOrFail($id);

        return response()->json($trader);
    }

    /**
     * Return trader data for editing (JSON for modal)
     */
    public function edit($id)
    {
        $trader = Trader::findOrFail($id);

        return response()->json($trader);
    }

    /**
     * Update the specified trader in database
     */
    public function update(Request $request, $id)
    {
        $trader = Trader::findOrFail($id);

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string',
            'category' => 'nullable|string|max:50',
        ]);

        // Treat empty string as null (global trader)
        if (isset($validated['category']) && $validated['category'] === '') {
            $validated['category'] = null;
        }

        if (auth()->check()) {
            $validated['updated_by'] = auth()->id();
        }

        $trader->update($validated);

        return response()->json(['message' => 'Trader updated successfully', 'trader' => $trader]);
    }

    /**
     * Soft-delete the specified trader
     */
    public function destroy($id)
    {
        $trader = Trader::findOrFail($id);
        $trader->update(['is_deleted' => true, 'is_active' => false]);

        return redirect()->route('trader')->with('success', 'Trader deleted successfully');
    }
}
