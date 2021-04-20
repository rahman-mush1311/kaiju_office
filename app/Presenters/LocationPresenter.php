<?php

namespace App\Presenters;

use App\Transformers\AgentTransformer;
use App\Transformers\LocationTransformer;
use App\Transformers\ProductTransformer;

class LocationPresenter extends BasePresenter
{

    public function present(): array
    {
        return [
            'id',
            'name',
            'details',
            'areas' => [AreaPresenter::class => ['areas']],
        ];
    }

    public function transformer()
    {
        return LocationTransformer::class;
    }
}
