<?php

namespace Raw\Webshop\Listeners;

use Illuminate\Events\Dispatcher;
use Raw\Webshop\Events\OrderCreated;
use Raw\Webshop\Events\PaymentReceived;
use Raw\Webshop\Jobs\SendOrderCreatedEmail;
use Raw\Webshop\Jobs\SendPaymentReceivedEmail;

class OrderEventSubscriber
{
    public function handlePaymentReceived(OrderCreated $event): void
    {
        SendPaymentReceivedEmail::dispatch($event->order);
    }

    public function handleOrderReceived(PaymentReceived $event): void
    {
        SendOrderCreatedEmail::dispatch($event->order);
    }

    public function subscribe(Dispatcher $events): array
    {
        return [
            OrderCreated::class => "handlePaymentReceived",
            PaymentReceived::class => "handleOrderReceived",
        ];
    }
}
