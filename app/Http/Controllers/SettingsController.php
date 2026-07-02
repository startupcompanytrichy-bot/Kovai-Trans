<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\FinancialYear;
use App\Models\GstSetting;
use App\Models\Login;
use App\Models\Permission;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    // ── Settings Permissions (granular user-level) ────────────────────────────

    protected $settingPermissions = [
        'view_settings_financial_year',
        'view_settings_branch_default',
    ];

    public function permissionIndex()
    {
        $users = Login::active()->get();
        $permissions = Permission::whereIn('name', $this->settingPermissions)->get();
        return view('Settings.Permissions.index', compact('users', 'permissions'));
    }

    public function editPermissions($id)
    {
        $user = Login::active()->findOrFail($id);
        $permissions = Permission::whereIn('name', $this->settingPermissions)->get();
        $userModel = User::where('email', $user->email)->first();
        $userPermIds = $userModel ? $userModel->permissions->pluck('id')->toArray() : [];
        return view('Settings.Permissions.edit', compact('user', 'permissions', 'userPermIds'));
    }

    public function updatePermissions(Request $request, $id)
    {
        $login = Login::active()->findOrFail($id);
        $userModel = User::where('email', $login->email)->first();
        if (!$userModel) {
            return back()->with('error', 'User model not found.');
        }

        $permissions = Permission::whereIn('name', $this->settingPermissions)->pluck('id');

        $granted = $request->input('permissions', []);
        $syncIds = $permissions->filter(fn($pid) => in_array($pid, $granted))->values()->toArray();

        $userModel->permissions()->syncWithoutDetaching($syncIds);
        $userModel->permissions()->detach($permissions->diff(collect($syncIds))->values()->toArray());

        return back()->with('success', "Settings permissions updated for {$login->email}.");
    }

    // ── Settings Page ──────────────────────────────────────────────────────────

    public function index()
    {
        $financialYears = FinancialYear::orderBy('start_date', 'desc')->get();
        $currentFY      = FinancialYear::current();
        $branches       = Branch::where('is_deleted', false)->orderBy('branch_name')->get();
        $branchSettings = Setting::where('group', 'branch')->get()->keyBy('key');
        $limitSettings  = Setting::where('group', 'limit')->get()->keyBy('key');
        $companyLimit   = $limitSettings['company_limit']->value ?? '';
        $branchLimit    = $limitSettings['branch_limit']->value ?? '';

        $allSettings    = Setting::where('group', '!=', 'payroll')->orderBy('group')->orderBy('label')->get();
        $gstSettings    = GstSetting::orderBy('name')->get();

        return view('Settings.Settings', compact('financialYears', 'currentFY', 'branches', 'branchSettings', 'companyLimit', 'branchLimit', 'allSettings', 'gstSettings'));
    }

    // ── Financial Year CRUD ────────────────────────────────────────────────────

    public function storeFY(Request $request)
    {
        $request->validate([
            'start_year' => 'required|integer|min:2000|max:2100',
        ]);

        $startYear = (int) $request->start_year;
        $label     = FinancialYear::generateLabel($startYear);

        if (FinancialYear::where('label', $label)->exists()) {
            return back()->withErrors(['start_year' => "Financial year {$label} already exists."]);
        }

        $isFirst = FinancialYear::count() === 0;

        FinancialYear::create([
            'label'      => $label,
            'start_date' => "{$startYear}-04-01",
            'end_date'   => ($startYear + 1) . '-03-31',
            'is_default' => $isFirst,
            'is_active'  => true,
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', "Financial Year {$label} created successfully.");
    }

    public function setDefaultFY(Request $request, $id)
    {
        $fy = FinancialYear::findOrFail($id);

        FinancialYear::where('is_default', true)->update(['is_default' => false]);

        $fy->update([
            'is_default' => true,
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', "Financial Year {$fy->label} set as active year. All data will now be filtered to this year.");
    }

    public function destroyFY($id)
    {
        $fy = FinancialYear::findOrFail($id);

        if ($fy->is_default) {
            return back()->withErrors(['fy' => 'Cannot delete the active financial year. Set another year as active first.']);
        }

        $fy->delete();

        return back()->with('success', "Financial Year {$fy->label} deleted.");
    }

    // ── Branch Settings ───────────────────────────────────────────────────────

    public function updateBranchSettings(Request $request)
    {
        $request->validate([
            'default_branch' => 'nullable|exists:branches,id',
        ]);

        Setting::updateOrCreate(
            ['key' => 'default_branch'],
            ['value' => $request->default_branch, 'group' => 'branch', 'label' => 'Default Branch']
        );

        return back()->with('success', 'Branch settings updated successfully.');
    }

    // ── Limit Settings ────────────────────────────────────────────────────────

    public function updateLimitSettings(Request $request)
    {
        $request->validate([
            'company_limit' => 'nullable|integer|min:0',
            'branch_limit'  => 'nullable|integer|min:0',
        ]);

        Setting::updateOrCreate(
            ['key' => 'company_limit'],
            ['value' => $request->company_limit, 'group' => 'limit', 'label' => 'Company Add Limit']
        );

        Setting::updateOrCreate(
            ['key' => 'branch_limit'],
            ['value' => $request->branch_limit, 'group' => 'limit', 'label' => 'Branch Add Limit']
        );

        return back()->with('success', 'Account limits updated successfully.');
    }

    // ── Update Individual Setting ──────────────────────────────────────────────

    public function updateSetting(Request $request)
    {
        $request->validate([
            'key'   => 'required|string|exists:settings,key',
            'value' => 'nullable|string',
        ]);

        $setting = Setting::where('key', $request->key)->firstOrFail();
        $setting->update(['value' => $request->value]);

        return back()->with('success', "Setting \"{$setting->label}\" updated successfully.");
    }

    // ── Payroll Settings ──────────────────────────────────────────────────────

    public function updatePayrollSettings(Request $request)
    {
        $request->validate([
            'pf_percentage'         => 'required|numeric|min:0|max:100',
            'esi_percentage'        => 'required|numeric|min:0|max:100',
            'pf_employer_percentage'  => 'required|numeric|min:0|max:100',
            'esi_employer_percentage' => 'required|numeric|min:0|max:100',
            'pf_wage_ceiling'       => 'required|numeric|min:0',
            'esi_wage_ceiling'      => 'required|numeric|min:0',
            'tds_default'           => 'required|numeric|min:0',
        ]);

        $fields = [
            'pf_percentage', 'esi_percentage',
            'pf_employer_percentage', 'esi_employer_percentage',
            'pf_wage_ceiling', 'esi_wage_ceiling', 'tds_default',
        ];

        $labels = [
            'pf_percentage'          => 'PF Percentage (%)',
            'esi_percentage'         => 'ESI Percentage (%)',
            'pf_employer_percentage'  => 'PF Employer Percentage (%)',
            'esi_employer_percentage' => 'ESI Employer Percentage (%)',
            'pf_wage_ceiling'        => 'PF Wage Ceiling (₹)',
            'esi_wage_ceiling'       => 'ESI Wage Ceiling (₹)',
            'tds_default'            => 'TDS Default Amount (₹)',
        ];

        foreach ($fields as $field) {
            Setting::updateOrCreate(
                ['key' => $field],
                ['value' => $request->input($field), 'group' => 'payroll', 'label' => $labels[$field]]
            );
        }

        return back()->with('success', 'Payroll settings updated successfully.');
    }

    public function storeGst(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'type'       => 'required|in:CGST+SGST,IGST',
            'percentage' => 'required|numeric|min:0|max:100',
        ]);

        GstSetting::create($request->only('name', 'type', 'percentage'));

        if ($request->ajax() || $request->expectsJson()) {
            $gstSettings = GstSetting::orderBy('name')->get();
            return response()->json([
                'success' => "GST \"{$request->name}\" added successfully.",
                'rows'    => view('Settings._gst_rows', compact('gstSettings'))->render(),
                'count'   => $gstSettings->count(),
            ]);
        }

        return back()->with('success', "GST \"{$request->name}\" added successfully.");
    }

    public function updateGst(Request $request, $id)
    {
        $gst = GstSetting::findOrFail($id);

        $request->validate([
            'name'       => 'required|string|max:255',
            'type'       => 'required|in:CGST+SGST,IGST',
            'percentage' => 'required|numeric|min:0|max:100',
        ]);

        $gst->update($request->only('name', 'type', 'percentage'));

        if ($request->ajax() || $request->expectsJson()) {
            $gstSettings = GstSetting::orderBy('name')->get();
            return response()->json([
                'success' => "GST \"{$gst->name}\" updated successfully.",
                'rows'    => view('Settings._gst_rows', compact('gstSettings'))->render(),
                'count'   => $gstSettings->count(),
            ]);
        }

        return back()->with('success', "GST \"{$gst->name}\" updated successfully.");
    }

    public function destroyGst($id)
    {
        $gst = GstSetting::findOrFail($id);
        $name = $gst->name;
        $gst->delete();

        if (request()->ajax() || request()->expectsJson()) {
            $gstSettings = GstSetting::orderBy('name')->get();
            return response()->json([
                'success' => "GST \"{$name}\" deleted successfully.",
                'rows'    => view('Settings._gst_rows', compact('gstSettings'))->render(),
                'count'   => $gstSettings->count(),
            ]);
        }

        return back()->with('success', "GST \"{$name}\" deleted.");
    }
}
