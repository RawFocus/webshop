<?php

namespace Raw\Webshop\Services;

use Log;
use Uuid;
use Webshop;

use Raw\Webshop\Models\Order;

use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Checkout\Session;
use Stripe\Exception\InvalidRequestException;
use Raw\Webshop\Http\Requests\CheckoutRequest;


class PaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(env("STRIPE_PRIVATE_KEY"));
    }

    public function checkoutFromRequest(CheckoutRequest $checkoutRequest)
    {
        $decodedProducts = json_decode($checkoutRequest->products);

        // Create order
        $order = Order::create([
            "name" => $checkoutRequest->name,
            "email" => $checkoutRequest->email,
            "address_street" => $checkoutRequest->address_street,
            "address_country" => $checkoutRequest->address_country,
            "address_postal_code" => $checkoutRequest->address_country,
            "address_city" => $checkoutRequest->address_city,
            "total_price" => $this->calculateTotalPrice($decodedProducts)
        ]);

        // Attach products
        foreach ($decodedProducts as $productData)
        {
            // Fetch product
            $product = Webshop::findProductByUuid($productData->uuid);

            // Attach product to order
            $order->products()->attach($product->id, ['quantity' => $productData->quantity]);

            // Decrease stock from product
            $product->stock -= $productData->quantity;
            $product->save();
        }

        $session = $this->createPaymentSession($order);

        return $session->url;
    }

    private function calculateTotalPrice(array $productData)
    {
        $out = 0;
        foreach ($productData as $productItem)
        {
            $out += $productItem->price * $productItem->quantity;
        }  

        return $out;     
    }

    private function createPaymentSession(Order $order)
    {
        // Create session
        $sessionData = [
            "client_reference_id" => $order->id,
            // "success_url" => config("webshop.payments.urls.success"),
            // "cancel_url" => config("webshop.payments.urls.cancel"),
            "success_url" => "https://staging.klimbuddies.nl/",
            "cancel_url" => "https://staging.klimbuddies.nl/",
            "payment_method_types" => ['ideal'],
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
                        "name" => $product->title,
                    ],
                    // Stripe api handles 10,00 like 1000. Hence, why the value is multiplied by 100
                    "unit_amount_decimal" => round($product->price * 100)
                ],
                "quantity" => $product->pivot->quantity,
                "tax_rates" => [config("payments.tax_rates.high")],
            ];
        }

        return Session::create($sessionData, ["idempotency_key" => (string) Uuid::generate(4)]);
    }

    public function getPayment(string $paymentId): Charge|null
    {
        try
        {
            // Get the paymentIntent from Stripe
            $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentId);
            if (!$paymentIntent) return null;

            // Get all the charges that where used
            $chargeData = $paymentIntent->charges->data;
            if (!$chargeData) return null;

            // Assuming there was only a single charge
            $charge = Charge::retrieve($chargeData[0]->id);
            $charge->amount_refunded = $charge->amount_refunded / 100;
            $charge->amount = $charge->amount / 100;
            return $charge;
        }
        catch (InvalidRequestException $exception)
        {
            Log::error(
                "getPayment: InvalidRequestException exception occurred. " .
                $exception->getStripeCode() . " paymentId:" . $paymentId
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
    
    public function revertStockDecreases(Order $order)
    {
        foreach ($order->products as $product)
        {
            $product->stock += $product->pivot->quantity;
            $product->save();
        }
    }
}