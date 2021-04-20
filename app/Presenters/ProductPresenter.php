<?php

namespace App\Presenters;

use App\Transformers\ProductTransformer;

class ProductPresenter extends BasePresenter
{

    public function present(): array
    {
        return [
            'id',
            'name',
            'slug',
            'long_description',
            'short_description',
            'mrp',
            'list_price' => 'trade_price',
            'status',
            'image',
            'brands' => [BrandPresenter::class => ['brands']]
        ];
    }

    public function transformer()
    {
        return ProductTransformer::class;
    }
}
