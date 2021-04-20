<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceAuth extends Model
{
    protected $table = 'service_auth';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'client_id', 'client_secret', 'revoked'];

}
