<?php

namespace Raw\Webshop\Services;

use Exception;
use Log;
use Uuid;
use Webshop;

use Raw\Webshop\Models\Order;

use Stripe\Stripe;
use Stripe\Charge;
use \Stripe\PaymentIntent;
use Stripe\Checkout\Session;
use Stripe\Exception\InvalidRequestException;
use Raw\Webshop\Http\Requests\CheckoutRequest;


class PaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(env("STRIPE_PRIVATE_KEY"));
    }

    /**
     * Checkout from request
     *
     * @param CheckoutRequest $checkoutRequest
     * @return string
     */
    public function processCheckoutFromRequest(CheckoutRequest $checkoutRequest)
    {
        // Create order
        $order = Order::create([
            "name" => $checkoutRequest->name,
            "email" => $checkoutRequest->email,
            "address_street" => $checkoutRequest->address,
            "address_country" => $checkoutRequest->address_country,
            "address_postal_code" => $checkoutRequest->address_country,
            "address_city" => $checkoutRequest->address_city,
            "total_price" => Webshop::calculateTotalOrderPrice($checkoutRequest->products)
        ]);

        // Attach products
        foreach ($checkoutRequest->products as $productData)
        {
            // Fetch product
            $product = Webshop::findProductByUuid($productData["uuid"]);

            // Attach product to order
            $order->products()->attach($product->id, ['quantity' => $productData["quantity"]]);

            // Decrease stock from product
            $product->stock -= $productData["quantity"];
            $product->save();
        }

        $session = $this->createStripePaymentSession($order);

        return $session->url;
    }

    /**
     * Get a payment session from Stripe
     *
     * @param string $paymentId
     * @return StripeObject
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
                $exception->getStripeCode() . " paymentId:" . $paymentId
            );
            Log::error($exception);
            return collect([]);
        }
    }

    /**
     * Create a payment session
     *
     * @param Order $order
     * @return StripeObject
     */
    private function createStripePaymentSession(Order $order)
    {
        // Create session
        $sessionData = [
            "client_reference_id" => $order->uuid,
            "success_url" => config("webshop.payments.urls.success"),
            "cancel_url" => config("webshop.payments.urls.cancel"),
            "payment_method_types" => [config("webshop.payments.payment_method_types")],
            "mode" => "payment",
            "metadata" => [
                "source" => env("APP_ENV"),
            ]
        ];

        // Add products to session
        foreach ($order->products as $product)
        {
            $sessionData["line_items"][] = [
                "price_data" => [
                    // Stripe only accepts lowercase for currency
                    "currency" => "eur",
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

        return Session::create($sessionData, ["idempotency_key" => (string) Uuid::generate(4)]);
    }

    /**
     * Get a payment from Stripe
     *
     * @param string $paymentId
     * @return Charge|null
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
                $exception . " paymentId:" . $paymentId
            );
            Log::error($exception);
            return null;
        }
    }

    public function markOrderAsPaid(Order $order)
    {
        $order->payment_status = "paid";
        $order->save();
    }
}