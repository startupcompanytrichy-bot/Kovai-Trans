<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\BelongsToBranch;

class Party extends Model
{
    use SoftDeletes, BelongsToBranch;

    protected $table = 'parties';

    protected $fillable = [
        'company_id', 'branch_id',
        'party_code',
        'company_name',
        'name',
        'email',
        'phone',
        'address',
        'party_type',
        'gst_no',
        'pan_no',
        'aadhaar_no',
        'opening_balance',
        'opening_balance_date',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'opening_balance_date' => 'date',
    ];

    /**
     * Get the user who created this party
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this party
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

