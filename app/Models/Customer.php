<?php

namespace App\Models;

use App\Filters\Filterable;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Customer extends Model implements AuthenticatableContract, JWTSubject
{
    use Authenticatable, Filterable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'auth_id',
        'email',
        'mobile',
        'status',
        'ecom_location_id',
        'ecom_area_id',
        'shop_name',
    ];

    protected $appends = [
        'area_name',
        'location_name',
    ];

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

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'ecom_area_id', 'ecom_area_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'ecom_location_id', 'ecom_location_id');
    }

    public function getAreaNameAttribute()
    {
        return trans_table_column(data_get($this, 'area.name'));
    }

    public function getLocationNameAttribute()
    {
        return trans_table_column(data_get($this, 'location.name'));
    }

}
