<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartLineItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cart_id',
        'product_id',
        'qty',
        'unit_price',
        'discounted_price',
        'item_total',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function distributor_product()
    {
        return $this->belongsTo(DistributorProduct::class, 'product_id', 'product_id');
    }
}
