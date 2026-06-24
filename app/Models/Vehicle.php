<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\BelongsToBranch;

class Vehicle extends Model
{
    use SoftDeletes, BelongsToBranch;

    protected $table = 'vehicles';

    protected $fillable = [
        'company_id', 'branch_id',
        'vehicle_name',
        'vehicle_number',
        'vehicle_type',
        'asset_make',
        'asset_type',
        'owner_type',
        'supplier_id',
        'engine_number',
        'chassis_number',
        'rc_number',
        'permit_number',
        'insurance_expiry_date',
        'fitness_expiry_date',
        'permit_expiry_date',
        'puc_expiry_date',
        'opening_balance',
        'opening_balance_date',
        'status',
        'created_by',
        'updated_by',
    ];

    /**
     * Vehicle has many documents
     */
    public function documents()
    {
        return $this->hasMany(VehicleDocument::class)->whereNull('deleted_at');
    }

    /**
     * Get supplier for this vehicle
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    /**
     * Get a specific document type (latest)
     */
    public function getDocument(string $type): ?VehicleDocument
    {
        return $this->documents()->where('document_type', $type)->latest()->first();
    }

    /**
     * Get the user who registered this vehicle
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this vehicle
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
