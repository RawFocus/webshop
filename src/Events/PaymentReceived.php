<?php

namespace Raw\Webshop\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Raw\Webshop\Models\Order;

class PaymentReceived
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
