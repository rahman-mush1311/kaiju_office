<?php


namespace App\Filters;


class SalesRepresentativeFilter extends QueryFilters
{

    public function statusFilter($value)
    {
        return $this->builder->where('sales_sales_representatives.status', $value);
    }

    public function searchFilter($value)
    {
        if (is_valid_mobile_number($value)) {
            return $this->builder->where('mobile', $value);
        }

        return $this->builder->leftJoin('users', 'users.id', '=', 'sales_representatives.user_id')
            ->whereRaw("LOWER(users.name) LIKE '%". strtolower($value) ."%'");
    }

}
