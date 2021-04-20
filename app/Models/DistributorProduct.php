<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class DistributorProduct extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'distributor_id',
        'product_id',
        'distributor_price',
        'min_order_qty',
        'status',
    ];

    public function agent()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
