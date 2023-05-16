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
}