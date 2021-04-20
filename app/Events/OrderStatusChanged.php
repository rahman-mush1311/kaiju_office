<?php

namespace App\Events;

class OrderStatusChanged extends Event
{
    public $orderId;
    public $newStatus;
    public $oldStatus;
    public $changedAt;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($orderId, $newStatus, $oldStatus, $changedAt)
    {
        $this->orderId = $orderId;
        $this->newStatus = $newStatus;
        $this->oldStatus = $oldStatus;
        $this->changedAt = $changedAt;
    }
}
