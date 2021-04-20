<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'customer_id',
        'sub_total',
        'total',
        'distributor_id',
        'delivery_charge',
    ];

    public function items()
    {
        return $this->hasMany(CartLineItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }
}
