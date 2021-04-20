<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;
    protected $filters = [];

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Product::with(['brands']);
        if(!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query;
    }

    public function map($row): array
    {
        $brands = '';
        if (!blank($row->brands)) {
            $brands = implode(",", $row->brands->plucK('id')->toArray());
        }

        return [
            $row->id,
            $row->name_en,
            $row->name_bn,
            $row->mrp,
            $row->trade_price,
            $row->short_description,
            $row->long_description,
            $brands,
        ];
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return [
            'product_id',
            'name_en',
            'name_bn',
            'mrp',
            'list_price',
            'short_description',
            'long_description',
            'brand_id',
        ];
    }
}
