<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToBranch;

class Payroll extends Model
{
    use BelongsToBranch;

    protected $table = 'payrolls';

    protected $fillable = [
        'company_id', 'branch_id', 'fin_year',
        'driver_id', 'employee_name', 'employee_type',
        'payroll_month',
        'basic_salary', 'hra', 'da', 'other_allowance', 'bonus',
        'pf', 'esi', 'tds', 'advance_deduction', 'other_deduction',
        'gross_salary', 'total_deductions', 'net_salary',
        'payment_mode', 'reference_no', 'payment_date', 'status',
        'notes', 'is_deleted', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'payroll_month'      => 'date',
        'payment_date'       => 'date',
        'basic_salary'       => 'decimal:2',
        'hra'                => 'decimal:2',
        'da'                 => 'decimal:2',
        'other_allowance'    => 'decimal:2',
        'bonus'              => 'decimal:2',
        'pf'                 => 'decimal:2',
        'esi'                => 'decimal:2',
        'tds'                => 'decimal:2',
        'advance_deduction'  => 'decimal:2',
        'other_deduction'    => 'decimal:2',
        'gross_salary'       => 'decimal:2',
        'total_deductions'   => 'decimal:2',
        'net_salary'         => 'decimal:2',
        'is_deleted'         => 'boolean',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Scoped to non-deleted records */
    public function scopeActive($query)
    {
        return $query->where('is_deleted', false);
    }

    /** Label helper */
    public function getPayrollMonthLabelAttribute(): string
    {
        return $this->payroll_month ? $this->payroll_month->format('M Y') : '';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'paid'    => '<span class="badge" style="background:#f0fff4;color:#38a169;border:1px solid #9ae6b4;font-size:11px;padding:3px 10px;border-radius:20px;">Paid</span>',
            'pending' => '<span class="badge" style="background:#fffbeb;color:#d97706;border:1px solid #fcd34d;font-size:11px;padding:3px 10px;border-radius:20px;">Pending</span>',
            default   => '<span class="badge" style="background:#f4f6fb;color:#596579;border:1px solid #d7dce5;font-size:11px;padding:3px 10px;border-radius:20px;">'.ucfirst($this->status).'</span>',
        };
    }
}
