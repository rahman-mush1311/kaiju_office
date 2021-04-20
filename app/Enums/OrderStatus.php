<?php


namespace App\Enums;


interface OrderStatus
{
    const CREATED = 1;
    const CONFIRMED = 10;
    const DELIVERED = 50;
    const REFUNDED = 60;
    const CANCELLED = 99;
}
