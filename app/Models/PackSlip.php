<?php

namespace App\Models;

use App\Models\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;

class PackSlip extends Model
{
    use BelongsToBranch;

    protected $table = 'pack_slips';

    protected $fillable = [
        'company_id', 'branch_id', 'fin_year',
        'lr_no', 'bill_no', 'lot_no', 'slip_date', 'pack_customer_id',
        'quality', 'no_of_bale', 'total_meter',
        'invoice_no', 'notes', 'created_by',
    ];

    protected $casts = [
        'slip_date'   => 'date',
        'no_of_bale'  => 'integer',
        'total_meter' => 'decimal:2',
        'fin_year'    => 'integer',
    ];

    public function customer()
    {
        return $this->belongsTo(PackCustomer::class, 'pack_customer_id');
    }

    public function baleItems()
    {
        return $this->hasMany(PackSlipBaleItem::class, 'pack_slip_id');
    }
}
