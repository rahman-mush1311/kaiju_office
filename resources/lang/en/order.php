<?php

use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;

return [
    'status' => [
        OrderStatus::CREATED => 'Created',
        OrderStatus::CONFIRMED => 'Confirmed',
        OrderStatus::DELIVERED => 'Delivered',
        OrderStatus::CANCELLED => 'Canceled',
        OrderStatus::REFUNDED => 'Declined',
    ],
    'payment_status' => [
        OrderPaymentStatus::PENDING => 'Pending',
        OrderPaymentStatus::PAID => 'Paid',
    ]
];
