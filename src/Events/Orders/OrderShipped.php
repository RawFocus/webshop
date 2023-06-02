<?php

namespace Raw\Webshop\Events\Orders;

use Raw\Webshop\Models\Order;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class OrderShipped
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $order;

    public $userId;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->orderId = $order->id;
    }

    public function getOrder()
    {
        return $this->order;
    }
}
