<?php

namespace App\Models;

use App\Enums\DistributorProductStatus;
use App\Filters\Filterable;
use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Ramsey\Uuid\Uuid;
use Spatie\Translatable\HasTranslations;
use Tymon\JWTAuth\Contracts\JWTSubject;


class Distributor extends Model implements AuthenticatableContract, JWTSubject
{
    use Authenticatable, HasTranslations, Filterable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'user_id',
        'name',
        'contact_person_name',
        'email',
        'mobile',
        'lat',
        'long',
        'status',
        'profile_image',
        'banner_image',
        'address',
        'minimum_order_value',
        'role',
    ];

    public $translatable = ['name'];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function products()
    {
        $relation = $this->belongsToMany(Product::class, 'distributor_products', 'distributor_id', 'product_id')
            ->withPivot('distributor_price', 'min_order_qty')
            ->wherePivot('status', '<>', DistributorProductStatus::DELETED);

        if (auth('distributor')->user() === null) {
            $relation->wherePivot('status', '<>', DistributorProductStatus::OUT_OF_STOCK);
        }

        return $relation;
    }

    public function area()
    {
        return $this->belongsToMany(Area::class, 'area_distributors', 'distributor_id', 'area_id')
            ->withPivot('location_id');
    }


    public function delivery_charge_rules()
    {
        return $this->belongsToMany(DeliveryChargeRule::class);
    }

    public function setOpeningTimeAttribute($value)
    {
        $this->attributes['opening_time'] = Carbon::parse($value)->format('H:i:s');
    }

    public function setClosingTimeAttribute($value)
    {
        $this->attributes['closing_time'] = Carbon::parse($value)->format('H:i:s');
    }

    public function getProfileImageAttribute()
    {
        return env('BIOSCOPE_MEDIA_URL', URL::to('/')) . '/' . ltrim($this->attributes['profile_image'], '/');
    }

    public function getBannerImageAttribute()
    {
        return env('BIOSCOPE_MEDIA_URL', URL::to('/')) . '/' . ltrim($this->attributes['banner_image'], '/');
    }
}
