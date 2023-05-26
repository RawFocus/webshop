<?php

namespace Raw\Webshop\Services;

use Raw\Webshop\Models\Order;
use Raw\Webshop\Models\Product;

use Illuminate\Database\Eloquent\Collection;

class WebshopService
{
    public function getProducts(): Collection
    {
        return Product::all();
    }

    public function getPreloadedProducts(): Collection
    {
        return Product::all()->map(function ($product) {
            $images = [];
            foreach ($product->images as $image) {
                $images[] = asset($image->path);
            }

            $product->images = $images;
            return $product;
        });
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

    public function findOrderById(int $id): ?Order
    {
        return Order::find($id);
    }

    public function findOrderByUuid(string $uuid): ?Order
    {
        return Order::where("uuid", $uuid)->first();
    }

    public function calculateTotalOrderPrice(array $productData)
    {
        $out = 0;
        foreach ($productData as $productItem)
        {
            $out += $productItem["price"] * $productItem["quantity"];
        }  

        return $out;     
    }

    public function revertStockDecreases(Order $order)
    {
        foreach ($order->products as $product)
        {
            $product->stock += $product->pivot->quantity;
            $product->save();
        }
    }

    public function getAllOrdersForCurrentUser()
    {
        if (!auth()->check()) return [];
        return Order::where("user_id", auth()->user()->id)->get();
    }
}