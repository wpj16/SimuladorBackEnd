<?php

namespace App\Database;

use Illuminate\Database\Schema\Grammars\PostgresGrammar;


class ColumnType extends PostgresGrammar
{
    /**
     * Create the column definition for an set type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */

    public function __call($method, $parameters)
    {
        $method = trim(str_replace('.', '_', $method));
        $methodArray = explode('_', $method);
        $method = lcfirst(implode('', array_map(function ($item) {
            return ucfirst(trim($item));
        }, $methodArray)));
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], $parameters);
        }
        return call_user_func_array([$this, 'typeNotDefined'], $parameters);
    }

    protected function typeNotDefined(\Illuminate\Support\Fluent $column)
    {
        return empty($column->type) ? 'type_not_defined_in_laravel' : $column->type;
    }
}
