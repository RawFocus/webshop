<?php

namespace RawFocus\Webshop\Services;

use Log;
use Uuid;
use Exception;
use WebshopOrders;
use WebshopProducts;

use Stripe\Stripe;
use Stripe\Charge;
use Stripe\PaymentIntent;
use Stripe\Checkout\Session;
use Stripe\Exception\InvalidRequestException;

use RawFocus\Webshop\Models\Order;

use RawFocus\Webshop\Events\Orders\OrderCreated;

use RawFocus\Webshop\Http\Requests\Checkout\CheckoutRequest;
use RawFocus\Webshop\Http\Requests\Checkout\PaymentRetryRequest;

class PaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(env("STRIPE_PRIVATE_KEY"));
    }

    /**
     * Checkout from request
     *
     * @param       CheckoutRequest $checkoutRequest
     * @return      string
     */
    public function processCheckoutFromRequest(CheckoutRequest $request)
    {
        $order = WebshopOrders::createFromCheckoutRequest($request);

        $session = $this->createStripePaymentSession($order);

        OrderCreated::dispatch($order);;

        return $session->url;
    }

    /**
     * Process payment retry from request
     *
     * @param       PaymentRetryRequest     $paymentRetryRequest
     * @return      string
     */
    public function processPaymentRetryFromRequest(PaymentRetryRequest $request)
    {
        $order = WebshopOrders::findOrderByUuid($request->order_uuid);

        $session = $this->createStripePaymentSession($order);

        return $session->url;
    }

    /**
     * Create a payment session
     *
     * @param       Order                   $order
     * @return      StripeObject
     */
    private function createStripePaymentSession(Order $order)
    {
        $sessionData = [
            "client_reference_id" => $order->uuid,
            "success_url" => config("webshop.payments.urls.success") . "/" . $order->uuid,
            "cancel_url" => config("webshop.payments.urls.cancel") . "/" . $order->uuid,
            "payment_method_types" => [config("webshop.payments.payment_method_types")],
            "mode" => "payment",
            "metadata" => [
                "source" => env("APP_ENV"),
            ],
            "line_items" => [],
        ];

        // Add products to session
        foreach ($order->products as $product)
        {
            $sessionData["line_items"][] = [
                "price_data" => [
                    "currency" => "eur", // Stripe only accepts lowercase for currency
                    "product_data" => [
                        "name" => $product->title["nl"],
                    ],
                    // Stripe api handles 10,00 like 1000. Hence, why the value is multiplied by 100
                    "unit_amount_decimal" => round($product->price * 100)
                ],
                "quantity" => $product->pivot->quantity,
                "tax_rates" => [config("webshop.payments.tax_rates.high")],
            ];
        }

        // Create the session
        $session = Session::create($sessionData, [
            "idempotency_key" => (string) Uuid::generate(4)
        ]);

        return $session;
    }

    /**
     * Get a payment session from Stripe
     *
     * @param       string                  $paymentId
     * @return      StripeObject
     */        
    public function getSession(string $paymentId)
    {
        try
        {
            // Get the payment session from Stripe
            $sessions = Session::all(["payment_intent" => $paymentId]);
            if (!$sessions) return collect([]);

            return collect($sessions->data)->first();
        }
        catch (InvalidRequestException $exception)
        {
            Log::error(
                "getSession: InvalidRequestException exception occurred. " .
                $exception->getStripeCode() . ", paymentId: " . $paymentId
            );
            Log::error($exception);
            return collect([]);
        }
    }

    /**
     * Get a payment from Stripe
     *
     * @param       string                  $paymentId
     * @return      Charge|null
     */
    public function getStripePayment(string $paymentId): Charge|null
    {
        try
        {
            // Get the paymentIntent from Stripe
            $paymentIntent = PaymentIntent::retrieve($paymentId);
            if (!$paymentIntent) return throw new PaymentNotFoundException("PaymentIntent not found: " . $paymentId);

            // Get all the charges that where used
            $chargeData = $paymentIntent->charges->data;
            if (!$chargeData) return throw new PaymentNotFoundException("No charge data found for paymentIntent: " . $paymentId);

            // Assuming there was only a single charge
            $charge = Charge::retrieve($chargeData[0]->id);
            $charge->amount_refunded = $charge->amount_refunded / 100;
            $charge->amount = $charge->amount / 100;
            return $charge;
        }
        catch (Exception $exception)
        {
            Log::error(
                "getStripePayment: exception occurred. " .
                $exception . ", paymentId: " . $paymentId
            );
            Log::error($exception);
            return null;
        }
    }

    /**
     * Mark order as paid
     * 
     * @param       Order  $order           The order
     */
    public function markOrderAsPaid(Order $order)
    {
        $order->payment_status = "paid";
        $order->save();
    }
}