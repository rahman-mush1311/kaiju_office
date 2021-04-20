<?php

namespace App\Exports;

use App\Models\DistributorProduct;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DistributorProductExport implements FromQuery, WithHeadings
{
    use Exportable;
    protected $filters = [];

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = DistributorProduct::query()
            ->select([
                'product_id',
                DB::raw("REPLACE(products.name_en, '\"', '')"),
                'distributor_price',
                'min_order_qty',
//                'agent_products.status',
            ])
            ->join('products', 'products.id', '=', 'distributor_products.product_id');

        if(!empty($this->filters['status'])) {
            $query->where('distributor_products.status', $this->filters['status']);
        }

        if(!empty($this->filters['product_status'])) {
            $query->where('products.status', $this->filters['product_status']);
        }

        if(!empty($this->filters['distributor_id'])) {
            $query->where('distributor_id', $this->filters['distributor_id']);
        }

        return $query;
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return [
            'product_id',
            'product_name',
            'distributor_price',
            'min_order_qty',
        ];
    }
}
