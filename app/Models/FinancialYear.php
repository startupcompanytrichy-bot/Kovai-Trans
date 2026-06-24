<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialYear extends Model
{
    protected $table = 'financial_years';

    protected $fillable = [
        'label',
        'start_date',
        'end_date',
        'is_default',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_default' => 'boolean',
        'is_active'  => 'boolean',
    ];

    /**
     * Get the currently active (default) financial year.
     */
    public static function current(): ?self
    {
        return static::where('is_default', true)->where('is_active', true)->first();
    }

    /**
     * Generate a label like "2025-26" from start year.
     */
    public static function generateLabel(int $startYear): string
    {
        $endYearShort = substr((string) ($startYear + 1), -2);
        return $startYear . '-' . $endYearShort;
    }
}
