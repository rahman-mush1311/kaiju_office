<?php


namespace App\Enums;


interface DistributorProductStatus
{
    const AVAILABLE = 1;
    const OUT_OF_STOCK = 2;
    const DELETED = 99;
}
