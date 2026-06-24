<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies';

    protected $fillable = [
        'fin_year',
        'company_code',
        'company_name',
        'business_type',
        'logo',
        'pan',
        'gst',
        'email',
        'phone',
        'phone2',
        'bank_name',
        'account_number',
        'account_holder_name',
        'ifsc_code',
        'branch_name',
        'upi_id',
        'place_of_supply',
        'country',
        'state',
        'district',
        'address',
        'pincode',
        'status',
        'created_by',
        'updated_by',
        'is_deleted',
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    public function getBusinessTypesDisplayAttribute(): string
    {
        if (empty($this->business_type)) {
            return '—';
        }

        $decoded = json_decode($this->business_type, true);

        if (is_array($decoded)) {
            return implode(', ', $decoded) ?: '—';
        }

        return $this->business_type;
    }
}
