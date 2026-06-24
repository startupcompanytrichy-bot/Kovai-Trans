<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToBranch;

class Trader extends Model
{
    use BelongsToBranch;

    protected $table = 'traders';

    protected $fillable = [
        'company_id', 'branch_id',
        'fin_year',
        'name', 'category', 'phone', 'address',
        'is_active', 'is_deleted',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'is_deleted' => 'boolean',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'trader_id');
    }
}
