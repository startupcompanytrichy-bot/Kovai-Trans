<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToBranch;

class Trip extends Model
{
    use BelongsToBranch;

    protected $table = 'trips';

    protected $fillable = [
        'company_id', 'branch_id',
        'fin_year',
        'trip_no',
        'booking_date',
        'trip_date',
        'expected_delivery_date',
        'vehicle_id',
        'driver_id',
        'supplier_id',
        'party_id',
        'from_location',
        'from_state',
        'from_district',
        'to_location',
        'to_state',
        'to_district',
        'distance_km',
        'material',
        'load_type',
        'quantity',
        'billing_type',
        'freight_amount',
        'advance_amount',
        'diesel_advance',
        'driver_bata',
        'toll_charges',
        'loading_charges',
        'unloading_charges',
        'other_expenses',
        'expense_notes',
        'balance_amount',
        'payment_status',
        'collected_amount',
        'collection_due_date',
        'payment_mode',
        'upi_details',
        'bank_details',
        'loading_date',
        'unloading_date',
        'start_kms_reading',
        'lr_no',
        'document_number',
        'document_path',
        'status',
        'workflow_status',
        'remarks',
        'invoice_no',
        'invoice_type',
        'invoice_status',
        'invoiced_at',
        'is_active',
        'is_deleted',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'trip_date'              => 'date',
        'booking_date'           => 'date',
        'expected_delivery_date' => 'date',
        'loading_date'           => 'date',
        'unloading_date'         => 'date',
        'collection_due_date'    => 'date',
        'quantity'               => 'decimal:2',
        'distance_km'            => 'decimal:2',
        'start_kms_reading'      => 'decimal:2',
        'freight_amount'         => 'decimal:2',
        'advance_amount'         => 'decimal:2',
        'diesel_advance'         => 'decimal:2',
        'driver_bata'            => 'decimal:2',
        'toll_charges'           => 'decimal:2',
        'loading_charges'        => 'decimal:2',
        'unloading_charges'      => 'decimal:2',
        'other_expenses'         => 'decimal:2',
        'balance_amount'         => 'decimal:2',
        'collected_amount'       => 'decimal:2',
        'is_active'              => 'boolean',
        'is_deleted'             => 'boolean',
        'invoiced_at'            => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function payments()
    {
        return $this->hasMany(TripPayment::class)->orderBy('paid_on', 'desc')->orderBy('id', 'desc');
    }

    // ── Computed helpers ───────────────────────────────────────────────────────

    /**
     * Total expenses for this trip
     */
    public function getTotalExpensesAttribute(): float
    {
        return (float) $this->driver_bata
            + (float) $this->toll_charges
            + (float) $this->loading_charges
            + (float) $this->unloading_charges
            + (float) $this->other_expenses
            + (float) $this->diesel_advance;
    }

    /**
     * Net profit (freight - total expenses)
     */
    public function getNetProfitAttribute(): float
    {
        return (float) $this->freight_amount - $this->total_expenses;
    }

    /**
     * Is this trip profitable?
     */
    public function getIsProfitableAttribute(): bool
    {
        return $this->net_profit >= 0;
    }

    /**
     * Outstanding collection amount
     */
    public function getOutstandingAmountAttribute(): float
    {
        return (float) $this->freight_amount - (float) $this->collected_amount;
    }
}
