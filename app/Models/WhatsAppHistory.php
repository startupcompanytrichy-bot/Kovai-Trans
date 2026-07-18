<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * WhatsAppHistory Model
 *
 * Stores a log of every WhatsApp message sent from the system.
 * Sources:
 *   - manual          → sent from the Daily Check In UI "Send" button
 *   - scheduled       → sent by the vehicle:send-document-reminders cron
 *   - update_trigger  → auto-sent when an expiry date is updated and falls within 15 days
 */
class WhatsAppHistory extends Model
{
    protected $table = 'whatsapp_histories';

    protected $fillable = [
        'company_id',
        'branch_id',
        'source',
        'vehicle_id',
        'contact_id',
        'document_type',
        'document_label',
        'expiry_date',
        'days_remaining',
        'contact_number',
        'message',
        'send_status',
        'error_message',
        'sent_at',
        'created_by',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'sent_at'     => 'datetime',
    ];

    // ── Relations ────────────────────────────────────────────────────────────

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function contact()
    {
        return $this->belongsTo(WhatsAppReminderContact::class, 'contact_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeSent($query)
    {
        return $query->where('send_status', 'sent');
    }

    public function scopeFailed($query)
    {
        return $query->where('send_status', 'failed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('sent_at', today());
    }
}
