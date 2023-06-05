<?php

namespace RawFocus\Webshop\Listeners;

use Illuminate\Events\Dispatcher;
use RawFocus\Webshop\Events\OrderArrived;
use RawFocus\Webshop\Events\OrderCreated;
use RawFocus\Webshop\Events\OrderShipped;
use RawFocus\Webshop\Events\PaymentReceived;
use RawFocus\Webshop\Jobs\SendOrderArrivedEmail;
use RawFocus\Webshop\Jobs\SendOrderCreatedEmail;
use RawFocus\Webshop\Jobs\SendOrderShippedEmail;
use RawFocus\Webshop\Jobs\SendPaymentReceivedEmail;

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
