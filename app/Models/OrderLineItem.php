<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class OrderLineItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'item_total',
        'quantity',
        'price',
        'discounted_price',
    ];


    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // This relation is only effective for distributor app
    public function distributor_product()
    {
        return $this->belongsTo(DistributorProduct::class, 'product_id', 'product_id')
            ->where('distributor_id', get_distributor_id());
    }
}
