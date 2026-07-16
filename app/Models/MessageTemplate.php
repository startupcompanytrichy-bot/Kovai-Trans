<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToBranch;

class MessageTemplate extends Model
{
    use BelongsToBranch;

    protected $table = 'message_templates';

    protected $fillable = [
        'company_id',
        'branch_id',
        'template_name',
        'message',
        'status',
        'created_by',
        'updated_by',
        'is_deleted',
    ];

    protected $casts = [
        'status'     => 'boolean',
        'is_deleted' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function vehicleReminderConfigs()
    {
        return $this->hasMany(\App\Models\VehicleReminderConfig::class, 'template_id');
    }
}
