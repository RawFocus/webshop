<?php

namespace RawFocus\Webshop\Events\Orders;

use RawFocus\Webshop\Models\Order;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class PaymentReceived
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $order;

    public $orderUuid;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->orderUuid = $order->uuid;
    }

    public function getOrder()
    {
        return $this->order;
    }
}
