<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToBranch;

class TripPayment extends Model
{
    use BelongsToBranch;

    protected $table = 'trip_payments';

    protected $fillable = [
        'company_id', 'branch_id',
        'fin_year',
        'trip_id',
        'amount',
        'payment_mode',
        'reference',
        'note',
        'paid_on',
        'created_by',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
        'paid_on' => 'date',
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
}
