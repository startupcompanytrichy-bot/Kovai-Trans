<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToBranch;

class Expense extends Model
{
    use BelongsToBranch;

    protected $table = 'expenses';

    protected $fillable = [
        'company_id', 'branch_id',
        'fin_year',
        'trip_id', 'vehicle_id', 'driver_id', 'trader_id',
        'category', 'amount', 'paid_amount', 'payment_status',
        'expense_date', 'payment_mode',
        'notes', 'bill_image', 'status',
        'approved_by', 'approved_at',
        'is_deleted', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'approved_at'  => 'datetime',
        'amount'       => 'decimal:2',
        'paid_amount'  => 'decimal:2',
        'is_deleted'   => 'boolean',
    ];

    /** Remaining balance on a credit expense */
    public function getBalanceAttribute(): float
    {
        return max(0, (float) $this->amount - (float) $this->paid_amount);
    }

    /** Is this credit expense fully settled? */
    public function getIsFullyPaidAttribute(): bool
    {
        return $this->payment_mode === 'credit' && $this->balance <= 0;
    }

    public static array $categories = [
        'fuel'        => ['label' => 'Fuel',        'icon' => 'ti-dropbox',      'color' => '#e53e3e', 'bg' => '#fff5f5'],
        'toll'        => ['label' => 'Toll',         'icon' => 'ti-map',          'color' => '#7c3aed', 'bg' => '#f5f3ff'],
        'driver_bata' => ['label' => 'Driver Bata',  'icon' => 'ti-user',         'color' => '#38a169', 'bg' => '#f0fff4'],
        'food'        => ['label' => 'Food',         'icon' => 'ti-cup',          'color' => '#d97706', 'bg' => '#fffbeb'],
        'repair'      => ['label' => 'Repair',       'icon' => 'ti-settings',     'color' => '#0369a1', 'bg' => '#f0f9ff'],
        'maintenance' => ['label' => 'Maintenance',  'icon' => 'ti-wrench',       'color' => '#b45309', 'bg' => '#fff8e1'],
        'parking'     => ['label' => 'Parking',      'icon' => 'ti-location-pin', 'color' => '#667eea', 'bg' => '#eef2ff'],
        'other'       => ['label' => 'Other',        'icon' => 'ti-more-alt',     'color' => '#8a94a6', 'bg' => '#f4f6fb'],
    ];

    public function trip()    { return $this->belongsTo(Trip::class); }
    public function vehicle() { return $this->belongsTo(Vehicle::class); }
    public function driver()  { return $this->belongsTo(Driver::class); }
    public function trader()  { return $this->belongsTo(Trader::class, 'trader_id'); }
    public function accessories() { return $this->hasMany(ExpenseAccessory::class, 'expense_id'); }
    public function payments()    { return $this->hasMany(ExpensePayment::class, 'expense_id')->orderBy('payment_date'); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }
    public function createdBy()  { return $this->belongsTo(User::class, 'created_by'); }
}
