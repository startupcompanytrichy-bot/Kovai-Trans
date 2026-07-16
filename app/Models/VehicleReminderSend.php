<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * VehicleReminderSend Model
 *
 * Stores details of all WhatsApp reminder messages sent for vehicle document
 * expiries. This table acts as an audit log for tracking which reminders
 * were sent, to whom, and when.
 *
 * Used by:
 *   - SendDocumentReminders command (automatic daily reminders)
 *   - DailyCheckInController (manual send from UI)
 *
 * Key features:
 *   - Tracks per-document, per-contact send status
 *   - Prevents duplicate sends via unique constraint checks
 *   - Stores the actual message content sent
 *   - Links to vehicle, contact, and reminder config for reference
 */
class VehicleReminderSend extends Model
{
    protected $table = 'vehicle_reminder_sends';

    /**
     * Fillable attributes
     */
    protected $fillable = [
        'company_id',       // Company this vehicle belongs to
        'branch_id',        // Branch this vehicle belongs to
        'vehicle_id',       // The vehicle with expiring document
        'contact_id',       // WhatsApp contact who received the message
        'config_id',        // Reminder config that triggered this send
        'document_type',    // Type: insurance_expiry_date, fitness_expiry_date, etc.
        'expiry_date',      // The document's expiry date
        'days_remaining',   // Days until expiry (negative = overdue)
        'message',          // The actual WhatsApp message sent
        'contact_number',   // Phone number the message was sent to
        'send_status',      // Status: pending, sent, failed
        'error_message',    // Error details if send failed
        'sent_at',          // Timestamp when message was sent
        'created_by',       // User who triggered the send (null for auto)
    ];

    /**
     * Attribute casts
     */
    protected $casts = [
        'expiry_date'    => 'date',
        'sent_at'        => 'datetime',
        'days_remaining' => 'integer',
    ];

    // ========================================================================
    // Relationships
    // ========================================================================

    /**
     * The vehicle this reminder is for
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * The WhatsApp contact who received the message
     */
    public function contact()
    {
        return $this->belongsTo(WhatsAppReminderContact::class, 'contact_id');
    }

    /**
     * The reminder config that triggered this send
     */
    public function config()
    {
        return $this->belongsTo(VehicleReminderConfig::class, 'config_id');
    }

    /**
     * The company this record belongs to
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * The branch this record belongs to
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * The user who triggered this send (null for automatic sends)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ========================================================================
    // Scopes
    // ========================================================================

    /**
     * Scope: Filter by send status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('send_status', $status);
    }

    /**
     * Scope: Filter by document type
     */
    public function scopeDocumentType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    /**
     * Scope: Filter by vehicle
     */
    public function scopeForVehicle($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    /**
     * Scope: Filter sent today
     */
    public function scopeSentToday($query)
    {
        return $query->whereDate('sent_at', now()->toDateString())
                     ->where('send_status', 'sent');
    }
}
