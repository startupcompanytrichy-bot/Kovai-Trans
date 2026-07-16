<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleReminderConfig extends Model
{
    protected $fillable = [
        'company_id',
        'branch_id',
        'template_id',
        'message',
        'duration',
        'time',
        'repeat_type',
        'created_by',
        'updated_by',
    ];

    public function template()
    {
        return $this->belongsTo(MessageTemplate::class, 'template_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
