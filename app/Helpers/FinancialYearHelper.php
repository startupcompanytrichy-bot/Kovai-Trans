<?php

use App\Models\FinancialYear;

if (!function_exists('currentFY')) {
    /**
     * Returns the active FinancialYear model, or null if none is set.
     */
    function currentFY(): ?FinancialYear
    {
        // Cache per-request so we don't hit DB on every call in a page load
        static $fy = false;
        if ($fy === false) {
            $fy = FinancialYear::current();
        }
        return $fy;
    }
}

if (!function_exists('currentFYLabel')) {
    /**
     * Returns the label string like "2025-26" or "All Years".
     */
    function currentFYLabel(): string
    {
        $fy = currentFY();
        return $fy ? $fy->label : 'All Years';
    }
}

if (!function_exists('applyFYFilter')) {
    /**
     * Apply the active financial year date filter to a query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $dateColumn  e.g. 'trip_date', 'expense_date'
     * @return \Illuminate\Database\Eloquent\Builder
     */
    function applyFYFilter($query, string $dateColumn)
    {
        $fy = currentFY();
        if ($fy) {
            $query->whereBetween($dateColumn, [
                $fy->start_date->format('Y-m-d'),
                $fy->end_date->format('Y-m-d'),
            ]);
        }
        return $query;
    }
}

if (!function_exists('applyFinYearFilter')) {
    /**
     * Apply the active financial year filter using the fin_year FK column.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    function applyFinYearFilter($query)
    {
        $fy = currentFY();
        if ($fy) {
            $query->where('fin_year', $fy->id);
        }
        return $query;
    }
}
