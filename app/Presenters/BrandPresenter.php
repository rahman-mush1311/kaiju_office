<?php

namespace App\Presenters;

use App\Transformers\BrandTransformer;
use App\Transformers\DistributorTransformer;

class BrandPresenter extends BasePresenter
{

    public function present(): array
    {
        return [
            'id',
            'name' => 'name',
        ];
    }

    public function transformer()
    {
        return BrandTransformer::class;
    }
}
