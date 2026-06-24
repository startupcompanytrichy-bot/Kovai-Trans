<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToBranch;

class VehicleEmi extends Model
{
    use BelongsToBranch;

    protected $table = 'vehicle_emis';

    protected static function booted(): void
    {
        static::creating(function (VehicleEmi $emi) {
            if ($emi->outstanding_balance === null) {
                $emi->outstanding_balance = $emi->loan_amount ?? 0;
            }
        });
    }

    protected $fillable = [
        'company_id', 'branch_id',
        'fin_year',
        'vehicle_id',
        'financier_name',
        'contract_no',
        'loan_amount',
        'interest_amount',
        'insurance_amount',
        'total_payable',
        'emi_amount',
        'interest_rate',
        'loan_start_date',
        'loan_end_date',
        'agreement_date',
        'first_instalment_date',
        'last_instalment_date',
        'total_emis',
        'paid_emis',
        'next_due_date',
        'outstanding_balance',
        'asset_make',
        'asset_type',
        'status',
        'notes',
        'is_deleted',
        'created_by',
        'updated_by',
        'reminder_sent',
        'reminder_sent_at',
    ];

    protected $casts = [
        'loan_start_date'      => 'date',
        'loan_end_date'        => 'date',
        'next_due_date'        => 'date',
        'agreement_date'       => 'date',
        'first_instalment_date'=> 'date',
        'last_instalment_date' => 'date',
        'reminder_sent_at'     => 'datetime',
        'loan_amount'          => 'decimal:2',
        'interest_amount'      => 'decimal:2',
        'insurance_amount'     => 'decimal:2',
        'total_payable'        => 'decimal:2',
        'emi_amount'           => 'decimal:2',
        'outstanding_balance'  => 'decimal:2',
        'interest_rate'        => 'decimal:2',
        'is_deleted'           => 'boolean',
        'reminder_sent'        => 'boolean',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
    public function payments()
    {
        return $this->hasMany(EmiPayment::class, 'vehicle_emi_id');
    }

    public function getPendingEmisAttribute(): int
    {
        return max(0, ($this->total_emis ?? 0) - ($this->paid_emis ?? 0));
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->next_due_date && $this->next_due_date->isPast() && $this->status === 'active';
    }
}
