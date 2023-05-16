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
        $totalPrice = 0;

        // Fetch products
        $products = [];
        foreach ($checkoutRequest->products as $productUuid)
        {
            $product = $this->findProductByUuid($productUuid);
            $totalPrice += $product->price;
        }
        
        // Fetch the user
        $user = auth()->user();

        // Create order
        $order = Order::create([
            "name" => $user->name,
            "email" => $user->email,
            "address_street" => $checkoutRequest->address_street,
            "address_country" => $checkoutRequest->address_country,
            "address_postal_code" => $checkoutRequest->address_country,
            "address_city" => $checkoutRequest->address_city,
            "total_price" => $totalPrice
        ]);

        foreach ($products as $product)
        {
            $order->orders()->attach([$product->id]);
        }

        // Notification
        // TODO
    }

    public function payment()
    {
        // todo
    }
}