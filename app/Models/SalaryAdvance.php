<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToBranch;

class SalaryAdvance extends Model
{
    use BelongsToBranch;

    protected $table = 'salary_advances';

    protected $fillable = [
        'company_id', 'branch_id', 'fin_year',
        'driver_id', 'trip_id', 'employee_name',
        'amount', 'advance_date',
        'payment_mode', 'reference_no',
        'recovered_amount', 'status',
        'notes', 'is_deleted', 'created_by',
    ];

    protected $casts = [
        'advance_date'     => 'date',
        'amount'           => 'decimal:2',
        'recovered_amount' => 'decimal:2',
        'is_deleted'       => 'boolean',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function trip()
    {
        return $this->belongsTo(\App\Models\Trip::class);
    }

    public function expenses()
    {
        return $this->hasMany(\App\Models\Expense::class, 'advance_id');
    }

    /** Outstanding balance */
    public function getPendingAmountAttribute(): float
    {
        return max(0, (float) $this->amount - (float) $this->recovered_amount);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'recovered' => '<span class="badge" style="background:#f0fff4;color:#38a169;border:1px solid #9ae6b4;font-size:11px;padding:3px 10px;border-radius:20px;">Recovered</span>',
            'partial'   => '<span class="badge" style="background:#eef4fd;color:#2c7be5;border:1px solid #93c5fd;font-size:11px;padding:3px 10px;border-radius:20px;">Partial</span>',
            default     => '<span class="badge" style="background:#fffbeb;color:#d97706;border:1px solid #fcd34d;font-size:11px;padding:3px 10px;border-radius:20px;">Pending</span>',
        };
    }
}
