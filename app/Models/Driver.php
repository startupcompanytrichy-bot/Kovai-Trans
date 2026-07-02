<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToBranch;

class Driver extends Model
{
    use BelongsToBranch;

    protected $table = 'drivers';

    protected $fillable = [
        'company_id', 'branch_id',
        'name',
        'driver_type',
        'license_number',
        'mobile',
        'alternative_mobile',
        'aadhar_number',
        'pan_number',
        'bank_name_id',
        'account_number',
        'ifsc_code',
        'branch_name',
        'dob',
        'state',
        'district',
        'city',
        'postal_code',
        'address',
        'remarks',
        'driver_photo',
        'aadhar_photo',
        'pan_photo',
        'license_photo',
        'is_active',
        'is_deleted',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'is_deleted' => 'boolean',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_name_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
