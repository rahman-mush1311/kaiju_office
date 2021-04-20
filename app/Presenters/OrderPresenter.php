<?php

namespace App\Presenters;

use App\Transformers\ProductTransformer;

class OrderPresenter extends BasePresenter
{

    public function present(): array
    {
        return [
            'id',
            'tracking_id',
            'customer' => [CustomerPresenter::class => ['customer']],
            'customer_mobile',
            'status',
            'payment_status',
            'sub_total',
            'total',
            'delivery_charge',
            'remarks',
            'misc',
            'rating',
            'delivery_address' => 'address',
            'line_items' => [OrderLineItemPresenter::class => ['line_items']],
            'created_at',
            'updated_at',
            'distributor' => [DistributorPresenter::class => ['distributor']],
            'sales_representative' => [SalesRepresentativePresenter::class => ['sales_representative']]
        ];
    }
}
