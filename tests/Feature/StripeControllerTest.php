<?php

namespace Tests\Unit\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use RawFocus\Webshop\database\factories\OrderFactory;
use RawFocus\Webshop\Enums\PaymentStatusEnum;
use RawFocus\Webshop\Tests\TestCase;

class StripeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testWebhook()
    {
        $order = OrderFactory::new()->create([
            'payment_status' => 'pending',
            'total_price' => 2600,
        ]);

        $stripeData = array(
            "object" => "checkout.session",
            "allow_promotion_codes" => null,
            "amount_subtotal" => 2600,
            "amount_total" => 2600,
            "billing_collection" => null,
            "cancel_url" => "http://localhost/success",
            "client_reference_id" => $order->uuid,
            "currency" => "usd",
            "customer" => "cus_woeijweiruiencineftest",
            "customer_email" => null,
            "livemode" => false,
            "locale" => null,
            "metadata" => [
                "source" => env("APP_ENV"),
            ],
            "mode" => "payment",
            "payment_intent" => "test-payment-id",
            "payment_method_types" => ["card"],
            "payment_status" => "paid",
            "setup_intent" => null,
            "shipping" => null,
            "shipping_collection" => null,
            "submit_type" => null,
            "subscription" => null,
            "success_url" => "http://localhost/success",
            "total_details" => array(
                "amount_discount" => 0,
                "amount_tax" => 0
            )
        );

        $request = array(
            "id" => "evt_1NDCY9KnEV82zsZarW2BTgY7",
            "object" => "event",
            "api_version" => "2020-08-27",
            "created" => 1685391617,
            "data" => array("object" => $stripeData),
            "livemode" => false,
            "pending_webhooks" => 1,
            "request" => array(
                "id" => null,
                "idempotency_key" => null
            ),
            "type" => "checkout.session.completed"
        );

        // Call method
        $this->assertEquals($order->payment_status, PaymentStatusEnum::PENDING);

        $this->post(route("stripe.webhook.endpoint"), $request)
            // ->assertStatus(200) // disable for debugging the route
            ->assertJson(["status" => "success"]);

        $order = $order->refresh();

        // Test if the payment status on the order has been set to paid
        $this->assertEquals($order->payment_status, PaymentStatusEnum::PAID);

        // Test if the stripe payment ID is set
        $this->assertEquals("test-payment-id", $order->payment_id);


    }


}
