<?php

namespace Raw\Webshop\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Raw\Webshop\Models\Order;

class PaymentReceivedMail extends Mailable
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
        $subject = __("emails.payment_received.subject");

        // Send the email
        return $this->subject($subject)
            ->view("emails.order-created", [
                "user" => $this->user,
                "link" => $link,
                "title" => __("emails.order_created.title"),
                "textOne" => __("emails.order_created.text_one", [
                    "name" => $this->order->name,
                ]),
                "textTwo" => __("emails.order_created.text_two"),
                "action" => __("emails.order_created.action"),
                "textThree" => __("emails.order_created.text_three"),
                "closing" => __("emails.general.closing"),
                "copyright" => __("emails.general.copyright", [
                    "year" => now()->format("Y")
                ]),
            ]);
    }
}
