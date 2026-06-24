<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MasterController extends Controller
{
    public function company()
    {
        $companies = Company::orderBy('company_name')->get();

        $stats = [
            'total'    => $companies->count(),
            'active'   => $companies->where('status', true)->count(),
            'inactive' => $companies->where('status', false)->count(),
            'with_gst' => $companies->whereNotNull('gst')->where('gst', '!=', '')->count(),
            'with_bank'=> $companies->whereNotNull('bank_name')->where('bank_name', '!=', '')->count(),
        ];

        return view('Company_Master.Company_Table', compact('companies', 'stats'));
    }

    public function add()
    {
        $companyLimit = setting('company_limit');
        if ($companyLimit !== null && $companyLimit !== '' && Company::count() >= (int) $companyLimit) {
            return redirect()->route('company')->with('error', 'This basic version allows only one company. Please contact support team for more.');
        }

        return view('Company_Master.New_Company');
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_code' => 'required|unique:companies,company_code',
            'company_name' => 'required',
            'business_type' => 'required',
            'pan' => 'required|unique:companies,pan',
            'gst' => 'nullable|unique:companies,gst',
            'email' => 'required|email|unique:companies,email',
            'country' => 'required',
            'state' => 'required',
            'district' => 'nullable|string|max:100',
            'address' => 'required',
            'pincode' => 'required',
        ], [
            'company_code.unique' => 'Company code already exists.',
            'pan.unique' => 'PAN number already exists.',
            'gst.unique' => 'GST number already exists.',
            'email.unique' => 'Email already exists.',
        ]);

        $companyLimit = setting('company_limit');
        if ($companyLimit !== null && $companyLimit !== '' && Company::count() >= (int) $companyLimit) {
            return redirect()->route('company')->with('error', 'This basic version allows only one company. Please contact support team for more.');
        }

        $data = [
            'company_code'        => $request->company_code,
            'company_name'        => $request->company_name,
            'business_type'       => $request->business_type,
            'pan'                 => $request->pan,
            'gst'                 => $request->gst,
            'email'               => $request->email,
            'phone'               => $request->phone,
            'phone2'              => $request->phone2,
            'country'             => $request->country,
            'state'               => $request->state,
            'district'            => $request->district,
            'address'             => $request->address,
            'pincode'             => $request->pincode,
            'bank_name'           => $request->bank_name,
            'account_number'      => $request->account_number,
            'account_holder_name' => $request->account_holder_name,
            'ifsc_code'           => $request->ifsc_code,
            'branch_name'         => $request->branch_name,
            'upi_id'              => $request->upi_id,
            'place_of_supply'     => $request->place_of_supply,
            'status'              => true,
            'created_by'          => Auth::id(),
        ];

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')
                ->store('company/logos', 'public');
        }

        Company::create($data);

        return redirect()
            ->route('company')
            ->with('success', 'Company saved successfully.');
    }

    public function view($id)
    {
        $company = Company::findOrFail($id);

        return view('Company_Master.View_Company', compact('company'));
    }

    public function edit($id)
    {
        $company = Company::findOrFail($id);

        return view('Company_Master.Edit_Company', compact('company'));
    }

    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);

        $request->validate([
            'company_code' => 'required|unique:companies,company_code,' . $id,
            'company_name' => 'required',
            'business_type' => 'required',
            'pan' => 'required|unique:companies,pan,' . $id,
            'gst' => 'nullable|unique:companies,gst,' . $id,
            'email' => 'required|email|unique:companies,email,' . $id,
            'country' => 'required',
            'state' => 'required',
            'district' => 'nullable|string|max:100',
            'address' => 'required',
            'pincode' => 'required',
        ]);

        $data = [
            'company_code'        => $request->company_code,
            'company_name'        => $request->company_name,
            'business_type'       => $request->business_type,
            'pan'                 => $request->pan,
            'gst'                 => $request->gst,
            'email'               => $request->email,
            'phone'               => $request->phone,
            'phone2'              => $request->phone2,
            'country'             => $request->country,
            'state'               => $request->state,
            'district'            => $request->district,
            'address'             => $request->address,
            'pincode'             => $request->pincode,
            'bank_name'           => $request->bank_name,
            'account_number'      => $request->account_number,
            'account_holder_name' => $request->account_holder_name,
            'ifsc_code'           => $request->ifsc_code,
            'branch_name'         => $request->branch_name,
            'upi_id'              => $request->upi_id,
            'place_of_supply'     => $request->place_of_supply,
            'updated_by'          => Auth::id(),
        ];

        if ($request->hasFile('logo')) {

            if (
                $company->logo &&
                Storage::disk('public')->exists($company->logo)
            ) {

                Storage::disk('public')
                    ->delete($company->logo);
            }

            $data['logo'] = $request->file('logo')
                ->store('company/logos', 'public');
        }

        $company->update($data);

        return redirect()
            ->route('company')
            ->with('success', 'Company updated successfully.');
    }

    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        $company->delete();

        return redirect()->route('company')->with('success', 'Company deleted successfully.');
    }

    public function branch()
    {
        $branches  = Branch::with('company')->orderBy('branch_name')->get();
        $companies = Company::orderBy('company_name')->get();

        $branchLimit = setting('branch_limit');
        $branchLimitReached = $branchLimit !== null && $branchLimit !== '' && $branches->count() >= (int) $branchLimit;

        return view('Branch_Master.Branch_Table', compact('branches', 'companies', 'branchLimitReached'));
    }

    public function branchAdd()
    {
        $companies = Company::orderBy('company_name')->get();

        return view('Branch_Master.New_Branch', compact('companies'));
    }

    public function branchStore(Request $request)
    {
        $request->validate([
            'company_id'  => 'required|exists:companies,id',
            'branch_code' => 'required|unique:branches,branch_code',
            'branch_name' => 'required',
            'email'       => 'nullable|email',
            'mobile'      => 'nullable|max:15',
            'pincode'     => 'nullable|max:10',
        ], [
            'branch_code.unique' => 'Branch code already exists.',
        ]);

        $branchLimit = setting('branch_limit');
        if ($branchLimit !== null && $branchLimit !== '' && Branch::count() >= (int) $branchLimit) {
            $message = 'This basic version allows limited branches. Please contact support team for more.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 403);
            }
            return back()->withErrors(['limit' => $message])->withInput();
        }

        $branch = Branch::create([
            'company_id'  => $request->company_id,
            'branch_code' => $request->branch_code,
            'branch_name' => $request->branch_name,
            'email'       => $request->email,
            'mobile'      => $request->mobile,
            'address'     => $request->address,
            'country'     => $request->country,
            'state'       => $request->state,
            'city'        => $request->city,
            'pincode'     => $request->pincode,
            'head_office' => filter_var($request->input('head_office', '0'), FILTER_VALIDATE_BOOLEAN),
            'status'      => $request->input('status', '1') === '1',
            'created_by'  => Auth::id(),
        ]);

        session()->flash('success', 'Branch saved successfully.');

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Branch saved successfully', 'branch' => $branch]);
        }

        return redirect()
            ->route('branch');
    }

    public function branchView($id)
    {
        $branch = Branch::with('company')->findOrFail($id);

        if (request()->expectsJson()) {
            return response()->json($branch);
        }

        return view('Branch_Master.View_Branch', compact('branch'));
    }

    public function branchEdit($id)
    {
        $branch = Branch::findOrFail($id);

        if (request()->expectsJson()) {
            return response()->json($branch);
        }

        $companies = Company::orderBy('company_name')->get();

        return view('Branch_Master.Edit_Branch', compact('branch', 'companies'));
    }

    public function branchUpdate(Request $request, $id)
    {
        $branch = Branch::findOrFail($id);

        $validated = $request->validate([
            'company_id'  => 'required|exists:companies,id',
            'branch_code' => 'required|unique:branches,branch_code,' . $id,
            'branch_name' => 'required',
            'email'       => 'nullable|email',
            'mobile'      => 'nullable|max:15',
            'pincode'     => 'nullable|max:10',
        ]);

        $branch->update([
            'company_id'  => $request->company_id,
            'branch_code' => $request->branch_code,
            'branch_name' => $request->branch_name,
            'email'       => $request->email,
            'mobile'      => $request->mobile,
            'address'     => $request->address,
            'country'     => $request->country,
            'state'       => $request->state,
            'city'        => $request->city,
            'pincode'     => $request->pincode,
            'head_office' => filter_var($request->input('head_office', '0'), FILTER_VALIDATE_BOOLEAN),
            'status'      => $request->input('status', '1') === '1',
            'updated_by'  => Auth::id(),
        ]);

        session()->flash('success', 'Branch updated successfully.');

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Branch updated successfully', 'branch' => $branch]);
        }

        return redirect()
            ->route('branch');
    }

    public function branchDestroy($id)
    {
        $branch = Branch::findOrFail($id);
        $branch->delete();

        return redirect()
            ->route('branch')
            ->with('success', 'Branch deleted successfully.');
    }
}
