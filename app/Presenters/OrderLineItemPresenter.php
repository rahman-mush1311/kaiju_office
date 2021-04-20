<?php

namespace App\Presenters;

use App\Transformers\ProductTransformer;

class OrderLineItemPresenter extends BasePresenter
{

    public function present(): array
    {
        return [
            'item_total',
            'qty',
            'unit_price',
            'discounted_price',
            'distributor_price' => 'distributor_product.distributor_price',
            'min_order_qty' => 'distributor_product.min_order_qty',
            'product' => [ProductPresenter::class => ['product']],
        ];
    }
}
