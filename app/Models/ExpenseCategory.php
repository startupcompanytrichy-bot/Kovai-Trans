<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $table = 'expense_categories';

    protected $fillable = [
        'key', 'label', 'icon', 'color', 'bg',
        'is_custom', 'is_active', 'created_by',
    ];

    protected $casts = [
        'is_custom' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Return all active categories as [key => [...]] map (same shape as Expense::$categories)
     */
    public static function allAsMap(): array
    {
        return static::where('is_active', true)
            ->orderBy('is_custom')
            ->orderBy('label')
            ->get()
            ->keyBy('key')
            ->map(fn($c) => [
                'label' => $c->label,
                'icon'  => $c->icon,
                'color' => $c->color,
                'bg'    => $c->bg,
            ])
            ->toArray();
    }
}
