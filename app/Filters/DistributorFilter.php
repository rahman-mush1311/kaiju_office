<?php


namespace App\Filters;


class DistributorFilter extends QueryFilters
{

    public function statusFilter($value)
    {
        return $this->builder->where('status', $value);
    }

    public function searchFilter($value)
    {
        if (is_valid_mobile_number($value)) {
            return $this->builder->where('mobile', $value);
        }

        return $this->builder->whereRaw("LOWER(name_en) LIKE '%". strtolower($value) ."%'")
            ->orWhereRaw("name_bn LIKE '%". strtolower($value) ."%'");
    }

}
