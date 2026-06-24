<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToBranch;

class EmiPayment extends Model
{
    use BelongsToBranch;

    protected $table = 'emi_payments';

    protected $fillable = [
        'company_id', 'branch_id',
        'fin_year',
        'vehicle_emi_id', 'due_month', 'payment_date', 'amount_paid',
        'penalty', 'payment_mode', 'reference_no',
        'receipt_image', 'notes', 'particulars', 'dr_cr', 'others_amount', 'created_by',
    ];

    protected $casts = [
        'due_month'      => 'date',
        'payment_date'   => 'date',
        'amount_paid'    => 'decimal:2',
        'penalty'        => 'decimal:2',
        'others_amount'  => 'decimal:2',
    ];

    public function emi() { return $this->belongsTo(VehicleEmi::class, 'vehicle_emi_id'); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
}
