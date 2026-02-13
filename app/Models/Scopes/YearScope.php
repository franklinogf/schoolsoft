<?php

namespace App\Models\Scopes;

use App\Models\Admin;
use App\Services\SchoolService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class YearScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where($model->qualifyColumn('year'), SchoolService::getCurrentYear());
    }
}
