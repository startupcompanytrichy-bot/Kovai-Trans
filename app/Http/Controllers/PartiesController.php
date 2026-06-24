<?php

namespace App\Http\Controllers;

use App\Models\Party;
use Illuminate\Http\Request;

class PartiesController extends Controller
{
    /**
     * Display list of all parties
     */
    public function parties()
    {
        $parties = Party::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('Parties.Parties', compact('parties'));
    }

    /**
     * Store a newly created party in database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:parties,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'party_type' => 'nullable',
            'gst_no' => 'nullable|string|max:15',
            'pan_no' => 'nullable|string|max:10',
            'opening_balance' => 'nullable|numeric|min:0',
            'opening_balance_date' => 'nullable|date',
        ]);

        $validated['status'] = 'active';

        // Only set created_by if user is authenticated
        if (auth()->check()) {
            $validated['created_by'] = auth()->id();
        }

        $party = Party::create($validated);

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Party added successfully', 'party' => $party]);
        }

        return redirect()->route('parties')->with('success', 'Party added successfully');
    }

    /**
     * Show party details
     */
    public function view($id)
    {
        $party = Party::findOrFail($id);

        return response()->json($party);
    }

    /**
     * Show the form for editing the specified party
     */
    public function edit($id)
    {
        $party = Party::findOrFail($id);

        return response()->json($party);
    }

    /**
     * Update the specified party in database
     */
    public function update(Request $request, $id)
    {
        $party = Party::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:parties,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'party_type' => 'nullable',
            'gst_no' => 'nullable|string|max:15',
            'pan_no' => 'nullable|string|max:10',
            'opening_balance' => 'nullable|numeric|min:0',
            'opening_balance_date' => 'nullable|date',
        ]);

        // Only set updated_by if user is authenticated
        if (auth()->check()) {
            $validated['updated_by'] = auth()->id();
        }

        $party->update($validated);

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Party updated successfully', 'party' => $party]);
        }

        return redirect()->route('parties')->with('success', 'Party updated successfully');
    }

    /**
     * Delete the specified party
     */
    public function destroy($id)
    {
        $party = Party::findOrFail($id);
        $party->delete();

        return redirect()->route('parties')->with('success', 'Party deleted successfully');
    }
}
