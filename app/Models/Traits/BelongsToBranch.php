<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToBranch
{
    public static function bootBelongsToBranch(): void
    {
        $defaultBranch = \setting('default_branch');

        if ($defaultBranch) {
            static::addGlobalScope('branch', function (Builder $query) use ($defaultBranch) {
                $table = $query->getModel()->getTable();
                $query->where($table . '.branch_id', $defaultBranch);
            });
        }

        static::creating(function ($model) {
            if (empty($model->branch_id) && \setting('default_branch')) {
                $model->branch_id = \setting('default_branch');
            }
            if (empty($model->company_id) && $model->branch_id) {
                $branch = \App\Models\Branch::find($model->branch_id);
                if ($branch) {
                    $model->company_id = $branch->company_id;
                }
            }
        });
    }
}
