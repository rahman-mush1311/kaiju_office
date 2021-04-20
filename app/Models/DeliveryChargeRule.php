<?php

namespace App\Models;

use App\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class DeliveryChargeRule extends Model
{
    use Filterable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'image',
        'status',
        'min_basket_size',
        'max_basket_size',
        'delivery_charge',
        'status',
    ];

    public function distributors()
    {
        return $this->belongsToMany(Distributor::class);
    }
}
