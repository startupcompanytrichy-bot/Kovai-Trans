<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppReminderContact extends Model
{
    protected $table = 'whatsapp_reminder_contacts';

    protected $fillable = ['name', 'mobile', 'company_id', 'branch_id', 'is_active', 'last_send_status', 'last_sent_at'];

    protected $casts = [
        'is_active'   => 'boolean',
        'last_sent_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
