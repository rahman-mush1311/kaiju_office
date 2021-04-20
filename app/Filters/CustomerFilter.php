<?php


namespace App\Filters;


class CustomerFilter extends QueryFilters
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

        return $this->builder->whereRaw("LOWER(name) LIKE '%". strtolower($value) ."%'")
            ->orWhereRaw("email LIKE '%". strtolower($value) ."%'");
    }

}
