<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\BelongsToBranch;

class VehicleDocument extends Model
{
    use SoftDeletes, BelongsToBranch;

    protected $table = 'vehicle_documents';

    protected $fillable = [
        'company_id', 'branch_id',
        'vehicle_id',
        'document_type',
        'document_label',
        'file_name',
        'file_path',
        'file_extension',
        'file_size',
        'notes',
        'uploaded_by',
    ];

    // Document type → human label map
    public static array $typeLabels = [
        'rc'        => 'RC Book',
        'insurance' => 'Insurance Copy',
        'fitness'   => 'Fitness Certificate',
        'permit'    => 'Permit Copy',
        'puc'       => 'PUC Certificate',
        'other'     => 'Other Document',
    ];

    // Document type → icon map
    public static array $typeIcons = [
        'rc'        => 'ti-id-badge',
        'insurance' => 'ti-shield',
        'fitness'   => 'ti-clipboard',
        'permit'    => 'ti-receipt',
        'puc'       => 'ti-check-box',
        'other'     => 'ti-folder',
    ];

    /**
     * Belongs to a Vehicle
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Belongs to the uploader user
     */
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the public URL for the file
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    /**
     * Check if file is a PDF
     */
    public function getIsPdfAttribute(): bool
    {
        return strtolower($this->file_extension) === 'pdf';
    }

    /**
     * Human-readable file size
     */
    public function getFileSizeHumanAttribute(): string
    {
        if (!$this->file_size) return '';
        $kb = $this->file_size / 1024;
        if ($kb < 1024) return round($kb, 1) . ' KB';
        return round($kb / 1024, 2) . ' MB';
    }
}
