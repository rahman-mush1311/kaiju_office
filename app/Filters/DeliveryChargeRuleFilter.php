<?php


namespace App\Filters;


class DeliveryChargeRuleFilter extends QueryFilters
{

    public function statusFilter($value)
    {
        return $this->builder->where('status', $value);
    }

    public function searchFilter($value)
    {
        return $this->builder->whereRaw("LOWER(name) LIKE '%". strtolower($value) ."%'");
    }

}
