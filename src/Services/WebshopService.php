<?php

namespace Raw\Webshop\Services;

use App\Http\Requests\Api\BuddyRequests\CheckoutRequest;
use Raw\Webshop\Models\Order;
use Raw\Webshop\Models\Product;

use Illuminate\Database\Eloquent\Collection;

class WebshopService
{
    public function getProducts(): Collection
    {
        return Product::all();
    }

    public function findProductById(int $id): ?Product
    {
        return Product::find($id);
    }

    public function findProductByUuid(string $uuid): ?Product
    {
        return Product::where("uuid", $uuid)->first();
    }

    public function findProductBySlug(string $slug): ?Product
    {
        return Product::where("slug", $slug)->first();
    }

    public function getOrders()
    {
        return Order::all();
    }

    public function getOrderById(int $id): ?Order
    {
        return Order::find($id);
    }

    public function getOrderByUuid(string $uuid): ?Order
    {
        return Order::where("uuid", $uuid)->first();
    }

    public function checkoutFromRequest(CheckoutRequest $checkoutRequest)
    {
        $user = auth()->user();
        $decodedProducts = json_decode($checkoutRequest->products);

        // Create order
        $order = Order::create([
            "name" => $user->name,
            "email" => $user->email,
            "address_street" => $checkoutRequest->address_street,
            "address_country" => $checkoutRequest->address_country,
            "address_postal_code" => $checkoutRequest->address_country,
            "address_city" => $checkoutRequest->address_city,
            "total_price" => $this->calculateTotalPrice($decodedProducts)
        ]);

        // Attach products
        foreach ($checkoutRequest->products as $productData)
        {
            // Fetch product
            $product = $this->findProductByUuid($productData->uuid);

            // Attach product to order
            $order->products()->attach($product->id, ['quantity' => $productData->quantity]);

            // Decrease stock from product
            $product->stock -= $productData->quantity;
            $product->save();
        }

        $this->createPaymentSession($order);
    }

    private function calculateTotalPrice(array $productData)
    {
        $out = 0;
        foreach ($productData as $productItem)
        {
            $out += $productItem->price * $productItem->pivot->quantity;
        }  

        return $out;     
    }

    private function createPaymentSession(Order $order)
    {
        // Create session
        $sessionData = [
            "client_reference_id" => $order->id,
            "success_url" => config("webshop.payments.urls.success"),
            "cancel_url" => config("webshop.payments.urls.cancel"),
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
                "price" => $product->price,
                "quantity" => $product->pivot->quantity,
                "tax_rates" => [config("payments.tax_rates.high")],
            ];
        }

        return Session::create($sessionData);
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