<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function byCompany(Request $request)
    {
        $companyId = $request->input('company_id');
        $selected  = $request->input('selected', []);
        if (!is_array($selected)) $selected = [$selected];

        $query = Branch::where('status', 1)->orderBy('branch_name');
        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $branches = $query->get(['id', 'branch_name', 'company_id']);

        $html = '';
        foreach ($branches as $branch) {
            $sel = in_array($branch->id, $selected) ? ' selected' : '';
            $html .= '<option value="'.$branch->id.'" data-company-id="'.$branch->company_id.'"'.$sel.'>'
                     .e($branch->branch_name).'</option>';
        }

        return response()->json(['html' => $html]);
    }
}
