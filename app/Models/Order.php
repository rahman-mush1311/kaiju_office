<?php

namespace App\Models;

use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Events\OrderStatusChanged;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'distributor_id',
        'customer_mobile',
        'status',
        'payment_status',
        'address',
        'sub_total',
        'total',
        'delivery_charge',
        'remarks',
        'misc',
        'tracking_id',
        'sales_representative_id',
    ];

    public const NEXT_STATUSES = [
        OrderStatus::CREATED => [
            OrderStatus::CREATED,
            OrderStatus::CONFIRMED,
            OrderStatus::CANCELLED,
        ],
        OrderStatus::CONFIRMED => [
            OrderStatus::CONFIRMED,
            OrderStatus::DELIVERED,
            OrderStatus::CANCELLED,
        ],
        OrderStatus::CANCELLED => [
            OrderStatus::CANCELLED,
        ],
        OrderStatus::DELIVERED => [
            OrderStatus::DELIVERED,
        ],
        OrderStatus::REFUNDED => [
            OrderStatus::REFUNDED,
        ]
    ];

    protected $casts = [
        'misc' => 'array',
    ];

    public function setMiscAttribute($value = [])
    {
        $this->attributes['misc'] = $value;
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function sales_representative()
    {
        return $this->belongsTo(SalesRepresentative::class);
    }

    public function line_items()
    {
        return $this->hasMany(OrderLineItem::class);
    }

    public function status_history()
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('id', 'desc');
    }

    protected static function boot()
    {
        parent::boot();

        static::updating(function (Model $order) {
            if($order->status == OrderStatus::DELIVERED && $order->payment_status != OrderPaymentStatus::PAID) {
                $order->payment_status = OrderPaymentStatus::PAID;
            }
        });

        static::saved(function (Model $order) {
            if ($order->isDirty('status')) {
                event(new OrderStatusChanged($order->id, $order->status, $order->getOriginal('status'), $order->updated_at));
            }
        });
    }
}
