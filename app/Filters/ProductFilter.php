<?php


namespace App\Filters;


class ProductFilter extends QueryFilters
{

    public function statusFilter($value)
    {
        return $this->builder->where('status', $value);
    }

    public function brandFilter($value)
    {
        return $this->builder->leftJoin('brand_products', 'products.id', '=', 'brand_products.product_id')
            ->where('brand_products.brand_id', $value);
    }

    public function searchFilter($value)
    {
        return $this->builder->whereRaw("LOWER(name_en) LIKE '%". strtolower($value) ."%'")
            ->orWhereRaw("name_bn LIKE '%". strtolower($value) ."%'");
    }

}
