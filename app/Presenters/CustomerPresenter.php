<?php

namespace App\Presenters;

use App\Transformers\AgentTransformer;
use App\Transformers\ProductTransformer;

class CustomerPresenter extends BasePresenter
{
    public function present(): array
    {
        return [
            'id',
            'auth_id',
            'contact_name' => 'name',
            'contact_number' => 'mobile',
            'shop_name',
            'area' => 'area_name',
            'location' => 'location_name',
            'email',
            'status',
        ];
    }
}
