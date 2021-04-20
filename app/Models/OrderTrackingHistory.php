<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class OrderTrackingHistory extends Model
{
    protected $table = 'order_tracking_histories';
    public $incrementing = false;
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'status',
        'previous_status',
        'changed_at',
    ];


}
