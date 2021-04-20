<?php


namespace App\Filters;


class BrandFilter extends QueryFilters
{

    public function statusFilter($value)
    {
        return $this->builder->where('status', $value);
    }

    public function searchFilter($value)
    {
        return $this->builder->whereRaw("LOWER(name_en) LIKE '%". strtolower($value) ."%'")
            ->orWhereRaw("name_bn LIKE '%". strtolower($value) ."%'");
    }

}
