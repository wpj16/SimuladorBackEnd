<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class SituationScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (method_exists($model, 'getDeleteAtColumn') && ($attribute = $model->getDeleteAtColumn())) {
            $builder->where($model->getTable() . '.' . $attribute, 1);
        }
    }
}
