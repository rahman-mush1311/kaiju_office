<?php

namespace App\Presenters;

use App\Transformers\DistributorTransformer;

class DistributorPresenter extends BasePresenter
{

    public function present(): array
    {
        return [
            'id',
            'name' => 'name',
            'contact_person_name',
            'email',
            'mobile',
            'lat',
            'long',
            'status',
            'minimum_order_value',
            'profile_image',
            'banner_image',
            'address',
            'brands' => 'products',
            'delivery_charge_rules',
            'role',
        ];
    }

    public function transformer()
    {
        return DistributorTransformer::class;
    }
}
