<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackSlipBaleItem extends Model
{
    protected $table = 'pack_slip_bale_items';

    protected $fillable = [
        'pack_slip_id', 'bale_no', 's_no', 'meter', 'weight',
    ];

    protected $casts = [
        'meter'  => 'decimal:2',
        'weight' => 'decimal:2',
    ];

    public function packSlip()
    {
        return $this->belongsTo(PackSlip::class);
    }
}
