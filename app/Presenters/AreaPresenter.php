<?php

namespace App\Presenters;

use App\Transformers\AgentTransformer;
use App\Transformers\AreaTransformer;
use App\Transformers\ProductTransformer;

class AreaPresenter extends BasePresenter
{

    public function present(): array
    {
        return [
            'id',
            'name',
            'lat',
            'long',
            'location' => [LocationPresenter::class => ['location']],
        ];
    }

    public function transformer()
    {
        return AreaTransformer::class;
    }
}
