<?php

namespace App\Presenters;

use App\Transformers\ProductTransformer;

class CartPresenter extends BasePresenter
{

    public function present(): array
    {
        return [
            'sub_total',
            'total',
            'delivery_charge',
            'line_items' => [CartLineItemPresenter::class => ['items']],
            'created_at',
            'updated_at',
            'distributor' => [DistributorPresenter::class => ['distributor']],
            'customer' => [CustomerPresenter::class => ['customer']],
        ];
    }
}
