<?php

namespace Raw\Webshop\Listeners;

use Illuminate\Events\Dispatcher;
use Raw\Webshop\Events\OrderArrived;
use Raw\Webshop\Events\OrderCreated;
use Raw\Webshop\Events\OrderShipped;
use Raw\Webshop\Events\PaymentReceived;
use Raw\Webshop\Jobs\SendOrderArrivedEmail;
use Raw\Webshop\Jobs\SendOrderCreatedEmail;
use Raw\Webshop\Jobs\SendOrderShippedEmail;
use Raw\Webshop\Jobs\SendPaymentReceivedEmail;

class OrderEventSubscriber
{
    public function handlePaymentReceived(OrderCreated $event): void
    {
        SendPaymentReceivedEmail::dispatch($event->getOrder());
    }

    public function handleOrderReceived(PaymentReceived $event): void
    {
        SendOrderCreatedEmail::dispatch($event->getOrder());
    }

    public function handleOrderArrived(OrderArrived $event): void
    {
        SendOrderArrivedEmail::dispatch($event->getOrder());
    }

    public function handleOrderShipped(OrderShipped $event): void
    {
        SendOrderShippedEmail::dispatch($event->getOrder());
    }

    public function subscribe(Dispatcher $events): array
    {
        return [
            PaymentReceived::class => "handlePaymentReceived",
            OrderCreated::class => "handleOrderReceived",
            OrderArrived::class => "handleOrderArrived",
            OrderShipped::class => "handleOrderShipped"
        ];
    }
}
