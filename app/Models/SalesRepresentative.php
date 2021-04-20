<?php


namespace App\Models;


use App\Filters\Filterable;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

class SalesRepresentative extends Model implements AuthenticatableContract, JWTSubject
{
    use Authenticatable, Filterable;

    protected $fillable = [
        'distributor_id',
        'user_id',
        'status',
        'mobile'
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

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
