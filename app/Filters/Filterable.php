<?php

namespace App\Filters;

trait Filterable
{
    public static function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }
}
