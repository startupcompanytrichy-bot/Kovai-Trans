<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\FinancialYear;
use App\Models\GstSetting;
use App\Models\Login;
use App\Models\Permission;
use App\Models\MessageTemplate;
use App\Models\Setting;
use App\Models\User;
use App\Models\WhatsAppReminderContact;
use App\Services\WhatsAppService;
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

    /**
     * Return [company_id, branch_id] for the current session login.
     */
    private function sessionOrgIds(): array
    {
        $loginUser = Login::find(session('loginId'));
        return [
            'company_id' => $loginUser->company_id ?? null,
            'branch_id'  => $loginUser->branch_id  ?? null,
        ];
    }

    /**
     * Apply company/branch scoping to any Eloquent query builder.
     * Super-admin (no company_id) sees all records.
     */
    private function scopeToOrg($query, array $org)
    {
        if ($org['company_id']) {
            $query->where('company_id', $org['company_id']);
        }
        if ($org['branch_id']) {
            $query->where('branch_id', $org['branch_id']);
        }
        return $query;
    }

    /**
     * Get scoped VehicleReminderConfig records for AJAX responses.
     */
    private function scopedVehicleConfigs()
    {
        return $this->scopeToOrg(
            \App\Models\VehicleReminderConfig::with(['template', 'company', 'branch'])->latest(),
            $this->sessionOrgIds()
        )->get();
    }

    /**
     * Get scoped MessageTemplate records for AJAX responses.
     */
    private function scopedMessageTemplates()
    {
        return $this->scopeToOrg(
            MessageTemplate::latest(),
            $this->sessionOrgIds()
        )->get();
    }

    public function index()
    {
        $financialYears = FinancialYear::orderBy('start_date', 'desc')->get();
        $currentFY      = FinancialYear::current();
        $branches       = Branch::where('is_deleted', false)->orderBy('branch_name')->get();
        $branchSettings = Setting::where('group', 'branch')->get()->keyBy('key');
        $limitSettings  = Setting::where('group', 'limit')->get()->keyBy('key');
        $companyLimit   = $limitSettings['company_limit']->value ?? '';
        $branchLimit    = $limitSettings['branch_limit']->value ?? '';

        $allSettings     = Setting::where('group', '!=', 'payroll')->orderBy('group')->orderBy('label')->get();
        $gstSettings     = GstSetting::orderBy('name')->get();
        $whatsappSettings = Setting::where('group', 'whatsapp')->get()->keyBy('key');

        $svc = app(\App\Services\WhatsAppService::class);
        $waStatus = $svc->getBaileysStatus();
        if (!empty($waStatus['connected']) && !empty($waStatus['number'])) {
            $fullNumber = ltrim($waStatus['number'], '0');
            if (strlen($fullNumber) === 12 && str_starts_with($fullNumber, '91')) {
                $fullNumber = substr($fullNumber, 2);
            }
            if (strlen($fullNumber) === 10) {
                Setting::updateOrCreate(
                    ['key' => 'whatsapp_connected_number'],
                    ['value' => $fullNumber, 'group' => 'whatsapp', 'label' => 'WhatsApp Connected Number']
                );
                $whatsappSettings = Setting::where('group', 'whatsapp')->get()->keyBy('key');
            }
        }

        $loginUser     = \App\Models\Login::find(session('loginId'));
        $userCompanyId = $loginUser->company_id ?? null;
        $userBranchId  = $loginUser->branch_id  ?? null;

        // Scope contacts, configs, and templates to the current session company/branch.
        // Super-admin (no company_id) sees all records.
        $contactQuery = WhatsAppReminderContact::orderBy('name');
        $configQuery  = \App\Models\VehicleReminderConfig::with(['template', 'company', 'branch'])->latest();
        $tmplQuery    = MessageTemplate::latest();

        if ($userCompanyId) {
            $contactQuery->where('company_id', $userCompanyId);
            $configQuery->where('company_id', $userCompanyId);
            $tmplQuery->where('company_id', $userCompanyId);
        }
        if ($userBranchId) {
            $contactQuery->where('branch_id', $userBranchId);
            $configQuery->where('branch_id', $userBranchId);
            $tmplQuery->where('branch_id', $userBranchId);
        }

        $waContacts       = $contactQuery->get();
        $vehicleConfigs   = $configQuery->get();
        $messageTemplates = $tmplQuery->get();

        return view('Settings.Settings', compact('financialYears', 'currentFY', 'branches', 'branchSettings', 'companyLimit', 'branchLimit', 'allSettings', 'gstSettings', 'whatsappSettings', 'waContacts', 'vehicleConfigs', 'userCompanyId', 'userBranchId', 'messageTemplates'));
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

    // ── WhatsApp Settings ──────────────────────────────────────────────────────

    public function updateWhatsAppSettings(Request $request)
    {
        $request->validate([
            'whatsapp_connected_number'    => 'nullable|string|max:10|regex:/^[0-9]{10}$/',
            'whatsapp_baileys_url'         => 'nullable|string|max:200',
            'whatsapp_emi_template'        => 'nullable|string|max:1000',
            'whatsapp_invoice_template'    => 'nullable|string|max:1000',
        ]);

        $fields = [
            'whatsapp_connected_number'   => 'WhatsApp Connected Number',
            'whatsapp_baileys_url'        => 'Baileys Service URL',
            'whatsapp_emi_template'       => 'EMI Template',
            'whatsapp_invoice_template'   => 'Invoice Template',
        ];

        foreach ($fields as $key => $label) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $request->input($key), 'group' => 'whatsapp', 'label' => $label]
            );
        }

        return back()->with('success', 'WhatsApp integration settings updated successfully.');
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

    public function getWhatsAppQr(WhatsAppService $whatsapp)
    {
        $status = $whatsapp->getBaileysStatus();
        $qr = $whatsapp->getBaileysQr();

        $number = $status['number'] ?? null;
        if ($number) {
            $fullNumber = ltrim($number, '0');
            if (strlen($fullNumber) === 12 && str_starts_with($fullNumber, '91')) {
                $fullNumber = substr($fullNumber, 2);
            }
            if (strlen($fullNumber) === 10) {
                Setting::updateOrCreate(
                    ['key' => 'whatsapp_connected_number'],
                    ['value' => $fullNumber, 'group' => 'whatsapp', 'label' => 'WhatsApp Connected Number']
                );
            }
        }

        return response()->json([
            'connected' => $status['connected'] ?? false,
            'dataUrl'   => $qr,
            'message'   => $qr ? null : ($status['error'] ?? 'No QR available'),
            'number'    => $fullNumber ?? null,
        ]);
    }

    public function testWhatsApp(Request $request, WhatsAppService $whatsapp)
    {
        $request->validate(['test_number' => 'required|string|max:12|regex:/^[0-9]{10,12}$/']);

        $number = $request->input('test_number');

        $status = $whatsapp->getBaileysStatus();
        if (!($status['connected'] ?? false)) {
            $err = $status['error'] ?? 'Baileys service not running';
            return back()->with('error', "WhatsApp not connected. {$err}");
        }

        if (!$whatsapp->canSend()) {
            return back()->with('error', 'Daily message limit reached. Try again tomorrow.');
        }

        $message = "✅ Test message from " . config('app.name') . ".\n\n"
                 . "Your WhatsApp integration is working!\n"
                 . "Daily limit: {$whatsapp->getRemainingCount()} messages remaining today.\n\n"
                 . "Sent at: " . now()->format('d M Y, h:i A');

        if ($whatsapp->sendMessage($number, $message)) {
            return back()->with('success', 'Test WhatsApp message sent successfully.');
        }

        return back()->with('error', 'Failed to send test message. Check Baileys service logs.');
    }

    public function connectWhatsApp(Request $request)
    {
        $request->validate([
            'number' => 'required|string|regex:/^[0-9]{10}$/',
        ]);

        $number = $request->input('number');
        $fullNumber = '91' . $number;

        Setting::updateOrCreate(
            ['key' => 'whatsapp_connected_number'],
            ['value' => $number, 'group' => 'whatsapp', 'label' => 'WhatsApp Connected Number']
        );

        $svc = app(WhatsAppService::class);
        $status = $svc->getBaileysStatus();
        $connected = $status['connected'] ?? false;

        if ($connected) {
            return response()->json([
                'connected' => true,
                'number'    => $fullNumber,
                'message'   => 'WhatsApp connected successfully.',
            ]);
        }

        $qrDataUrl = $svc->getBaileysQr();

        return response()->json([
            'connected' => false,
            'number'    => $fullNumber,
            'hasQr'     => !!$qrDataUrl,
            'qrDataUrl' => $qrDataUrl,
            'message'   => $qrDataUrl
                ? 'Please scan the QR code with your WhatsApp to link this device.'
                : 'Baileys service is starting. Please wait a moment and try again.',
        ]);
    }

    public function disconnectWhatsApp(WhatsAppService $whatsapp)
    {
        $rawUrl = Setting::getValue('whatsapp_baileys_url', '');
        $baileysUrl = rtrim($rawUrl ?: 'http://localhost:3001', '/');
        $authDir    = base_path('node-services/whatsapp-baileys/auth_info');

        if (is_dir($authDir)) {
            $files = glob($authDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'WhatsApp disconnected. Restart the Baileys service and scan the QR code again.']);
    }

    // ── Vehicle Reminder Settings ────────────────────────────────────────────

    public function storeVehicleReminderConfig(Request $request)
    {
        $request->validate([
            'template_id' => 'required|integer|exists:message_templates,id',
            'duration'    => 'required|string|max:50',
            'time'        => 'required|string|max:20',
        ]);

        $org  = $this->sessionOrgIds();
        $tmpl = MessageTemplate::findOrFail($request->template_id);

        \App\Models\VehicleReminderConfig::create([
            'company_id'  => $org['company_id'],
            'branch_id'   => $org['branch_id'],
            'template_id' => $tmpl->id,
            'message'     => $tmpl->message,
            'duration'    => $request->duration,
            'time'        => $request->time,
            'created_by'  => Auth::id(),
            'updated_by'  => Auth::id(),
        ]);

        if ($request->ajax() || $request->expectsJson()) {
            $vehicleConfigs = $this->scopedVehicleConfigs();
            return response()->json([
                'success' => 'Vehicle reminder config added successfully.',
                'rows'    => view('Settings._vehicle_reminder_rows', compact('vehicleConfigs'))->render(),
                'count'   => $vehicleConfigs->count(),
            ]);
        }

        return back()->with('success', 'Vehicle reminder config added successfully.');
    }

    public function updateVehicleReminderConfig(Request $request, $id)
    {
        $config = \App\Models\VehicleReminderConfig::findOrFail($id);

        $request->validate([
            'template_id' => 'required|integer|exists:message_templates,id',
            'duration'    => 'required|string|max:50',
            'time'        => 'required|string|max:20',
        ]);

        $tmpl = MessageTemplate::findOrFail($request->template_id);

        $config->update([
            'template_id' => $tmpl->id,
            'message'     => $tmpl->message,
            'duration'    => $request->duration,
            'time'        => $request->time,
            'updated_by'  => Auth::id(),
        ]);

        if ($request->ajax() || $request->expectsJson()) {
            $vehicleConfigs = $this->scopedVehicleConfigs();
            return response()->json([
                'success' => 'Vehicle reminder config updated successfully.',
                'rows'    => view('Settings._vehicle_reminder_rows', compact('vehicleConfigs'))->render(),
                'count'   => $vehicleConfigs->count(),
            ]);
        }

        return back()->with('success', 'Vehicle reminder config updated successfully.');
    }

    public function destroyVehicleReminderConfig(Request $request, $id)
    {
        $config = \App\Models\VehicleReminderConfig::findOrFail($id);
        $config->delete();

        if ($request->ajax() || $request->expectsJson()) {
            $vehicleConfigs = $this->scopedVehicleConfigs();
            return response()->json([
                'success' => 'Vehicle reminder config deleted.',
                'rows'    => view('Settings._vehicle_reminder_rows', compact('vehicleConfigs'))->render(),
                'count'   => $vehicleConfigs->count(),
            ]);
        }

        return back()->with('success', 'Vehicle reminder config deleted.');
    }

    // ── Message Template CRUD ─────────────────────────────────────────────────

    public function storeMessageTemplate(Request $request)
    {
        $request->validate([
            'template_name' => 'required|string|max:150',
            'message'       => 'required|string',
        ]);

        $org = $this->sessionOrgIds();

        MessageTemplate::create([
            'company_id'    => $org['company_id'],
            'branch_id'     => $org['branch_id'],
            'template_name' => trim($request->template_name),
            'message'       => trim($request->message),
            'status'        => true,
            'created_by'    => Auth::id(),
            'updated_by'    => Auth::id(),
        ]);

        if ($request->ajax() || $request->expectsJson()) {
            $messageTemplates = $this->scopedMessageTemplates();
            return response()->json([
                'success' => "Template \"{$request->template_name}\" added successfully.",
                'rows'    => view('Settings._message_template_rows', compact('messageTemplates'))->render(),
                'count'   => $messageTemplates->count(),
            ]);
        }

        return back()->with('success', "Template \"{$request->template_name}\" added successfully.");
    }

    public function updateMessageTemplate(Request $request, $id)
    {
        $template = MessageTemplate::findOrFail($id);

        $request->validate([
            'template_name' => 'required|string|max:150',
            'message'       => 'required|string',
        ]);

        $template->update([
            'template_name' => trim($request->template_name),
            'message'       => trim($request->message),
            'status'        => $request->boolean('status', $template->status),
            'updated_by'    => Auth::id(),
        ]);

        if ($request->ajax() || $request->expectsJson()) {
            $messageTemplates = $this->scopedMessageTemplates();
            return response()->json([
                'success' => "Template \"{$template->template_name}\" updated successfully.",
                'rows'    => view('Settings._message_template_rows', compact('messageTemplates'))->render(),
                'count'   => $messageTemplates->count(),
            ]);
        }

        return back()->with('success', "Template \"{$template->template_name}\" updated successfully.");
    }

    public function destroyMessageTemplate(Request $request, $id)
    {
        $template = MessageTemplate::findOrFail($id);
        $name = $template->template_name;

        // Check if any vehicle reminder config references this template
        $usedCount = \App\Models\VehicleReminderConfig::where('template_id', $id)->count();
        if ($usedCount > 0) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'error' => "Cannot delete \"{$name}\": it is used by {$usedCount} vehicle reminder config(s). Remove those configs first.",
                ], 422);
            }
            return back()->withErrors(['template' => "Cannot delete \"{$name}\": it is used by {$usedCount} vehicle reminder config(s). Remove those configs first."]);
        }

        $template->delete();

        if ($request->ajax() || $request->expectsJson()) {
            $messageTemplates = $this->scopedMessageTemplates();
            return response()->json([
                'success' => "Template \"{$name}\" deleted successfully.",
                'rows'    => view('Settings._message_template_rows', compact('messageTemplates'))->render(),
                'count'   => $messageTemplates->count(),
            ]);
        }

        return back()->with('success', "Template \"{$name}\" deleted.");
    }

    public function toggleMessageTemplate(Request $request, $id)
    {
        $template = MessageTemplate::findOrFail($id);
        $template->update([
            'status'     => !$template->status,
            'updated_by' => Auth::id(),
        ]);

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => 'Status updated.',
                'status'  => $template->status,
            ]);
        }

        return back()->with('success', 'Template status updated.');
    }
}
