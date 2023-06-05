<?php

namespace RawFocus\Webshop\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use RawFocus\Webshop\Models\Order;

class OrderShippedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Generate link
        $link = env("FRONTEND_WEB_URL") . "/webshop/order/" . $this->order->uuid;

        // Generate subject
        $subject = __("webshop::emails.order_shipped.subject");

        // Send the email
        return $this->subject($subject)
            ->view("webshop::emails.webshop.order-shipped", [
                "link" => $link,
                "title" => __("webshop::emails.order_shipped.title"),
                "textOne" => __("webshop::emails.order_shipped.text_one", [
                    "name" => $this->order->name,
                ]),
                "textTwo" => __("webshop::emails.order_shipped.text_two"),
                "action" => __("webshop::emails.order_shipped.action"),
                "textThree" => __("webshop::emails.order_shipped.text_three"),
                "closing" => __("webshop::emails.general.closing"),
                "copyright" => __("webshop::emails.general.copyright", [
                    "year" => now()->format("Y")
                ]),
            ]);
    }
}
