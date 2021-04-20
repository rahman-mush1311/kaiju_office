<?php

namespace App\Listeners;

use App\Events\ExampleEvent;
use App\Events\OrderStatusChanged;
use App\Models\OrderTrackingHistory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class StoreOrderStatusHistory
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderStatusChanged  $event
     * @return void
     */
    public function handle(OrderStatusChanged $event)
    {
        $orderStatus = new OrderTrackingHistory();
        $orderStatus->fill([
            'order_id' =>  $event->orderId,
            'status' =>  $event->newStatus,
            'previous_status' =>  $event->oldStatus,
            'changed_at' =>  $event->changedAt,
        ]);

        $orderStatus->save();
    }
}
