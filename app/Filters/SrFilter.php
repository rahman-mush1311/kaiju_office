<?php


namespace App\Filters;


class SrFilter extends QueryFilters
{
    public function searchFilter($value)
    {
        if (is_valid_mobile_number($value)) {
            return $this->builder->where('mobile', $value);
        }

        $this->builder->select('sales_representatives.id as id', 'sales_representatives.*');
        $this->builder->join('users','sales_representatives.user_id','=','users.id');
        return $this->builder->whereRaw("LOWER(users.name) LIKE '%". strtolower($value) ."%'")
            ->orWhereRaw("email LIKE '%". strtolower($value) ."%'");
    }

}
