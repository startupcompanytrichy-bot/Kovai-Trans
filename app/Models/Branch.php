<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $table = 'branches';

    protected $fillable = [
        'company_id',
        'branch_code',
        'branch_name',
        'email',
        'mobile',
        'address',
        'country',
        'state',
        'city',
        'pincode',
        'head_office',
        'status',
        'created_by',
        'updated_by',
        'is_deleted',
    ];

    protected $casts = [
        'head_office' => 'boolean',
        'status' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
