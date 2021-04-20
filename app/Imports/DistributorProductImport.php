<?php

namespace App\Imports;

use App\Models\DistributorProduct;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DistributorProductImport implements ToModel, WithHeadingRow
{
    protected $distributorId = null;

    public function __construct($distributorId)
    {
        $this->distributorId = $distributorId;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new DistributorProduct([
            'product_id' => $row['product_id'],
            'distributor_id' => $this->distributorId,
            'distributor_price' => $row['distributor_price'],
            'min_order_qty' => $row['min_order_qty'],
        ]);
    }
}
