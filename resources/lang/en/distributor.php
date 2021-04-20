<?php

return [
    'status' => [
        \App\Enums\DistributorStatus::ACTIVE => 'Active',
        \App\Enums\DistributorStatus::INACTIVE => 'Inactive',
    ],
    'product_status' => [
        \App\Enums\DistributorProductStatus::AVAILABLE => 'Available',
        \App\Enums\DistributorProductStatus::OUT_OF_STOCK => 'Out Of Stock',
    ]
];
