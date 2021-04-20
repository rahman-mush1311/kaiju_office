<?php

namespace App\Presenters;

use App\Transformers\ProductTransformer;

class DistributorProductPresenter extends BasePresenter
{

    protected $default = [];

    public function present(): array
    {
        return [
            'id',
            'distributor_id',
            'product_id',
            'distributor_price',
            'min_order_qty',
            'status',
            'product' => [ProductPresenter::class => ['product']],
        ];
    }
}
