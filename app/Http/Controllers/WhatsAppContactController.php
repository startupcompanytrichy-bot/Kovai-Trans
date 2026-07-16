<?php

namespace App\Http\Controllers;

use App\Models\Login;
use App\Models\WhatsAppReminderContact;
use Illuminate\Http\Request;

class WhatsAppContactController extends Controller
{
    /**
     * Resolve company_id and branch_id from the current session login.
     */
    private function currentSession(): array
    {
        $loginUser = Login::find(session('loginId'));
        return [
            'company_id' => $loginUser->company_id ?? null,
            'branch_id'  => $loginUser->branch_id  ?? null,
        ];
    }

    /**
     * Build a scoped query for the current session's company/branch.
     * Super-admin (no company) sees all.
     */
    private function scopedQuery()
    {
        $s = $this->currentSession();
        $q = WhatsAppReminderContact::query();
        if ($s['company_id']) {
            $q->where('company_id', $s['company_id']);
        }
        if ($s['branch_id']) {
            $q->where('branch_id', $s['branch_id']);
        }
        return $q;
    }

    public function index()
    {
        $contacts = $this->scopedQuery()->with(['company', 'branch'])->orderBy('name')->get();
        return view('Settings.whatsapp-contacts', compact('contacts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:100',
            'mobile' => 'required|string|digits:10|regex:/^[6-9][0-9]{9}$/',
        ]);

        $s = $this->currentSession();

        WhatsAppReminderContact::create([
            'name'       => trim($request->name),
            'mobile'     => trim($request->mobile),
            'company_id' => $s['company_id'],
            'branch_id'  => $s['branch_id'],
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => 'Contact added successfully.']);
        }
        return back()->with('success', 'Contact added successfully.');
    }

    public function update(Request $request, $id)
    {
        $contact = WhatsAppReminderContact::findOrFail($id);

        $request->validate([
            'name'   => 'required|string|max:100',
            'mobile' => 'required|string|digits:10|regex:/^[6-9][0-9]{9}$/',
        ]);

        $contact->update([
            'name'   => trim($request->name),
            'mobile' => trim($request->mobile),
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => 'Contact updated successfully.']);
        }
        return back()->with('success', 'Contact updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $contact = WhatsAppReminderContact::findOrFail($id);
        $contact->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => 'Contact deleted successfully.']);
        }
        return back()->with('success', 'Contact deleted successfully.');
    }

    public function toggle(Request $request, $id)
    {
        $contact = WhatsAppReminderContact::findOrFail($id);
        $contact->update(['is_active' => !$contact->is_active]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => 'Status updated.', 'is_active' => $contact->is_active]);
        }
        return back()->with('success', 'Contact status updated.');
    }
}
