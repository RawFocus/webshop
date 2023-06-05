<?php

namespace RawFocus\Webshop\Jobs;

use Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use RawFocus\Webshop\Mail\OrderCreatedMail;
use RawFocus\Webshop\Mail\OrderShippedMail;
use RawFocus\Webshop\Models\Order;

class SendOrderShippedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }
    
    public function handle()
    {
        Mail::to($this->order->email)
            ->send(new OrderShippedMail($this->order));
    }
}
