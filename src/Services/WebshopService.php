<?php

namespace Raw\Webshop\Services;

use Raw\Webshop\Models\Order;
use Raw\Webshop\Models\Product;

class WebshopService
{
    public function getProducts()
    {
        return Product::all();
    }

    public function getOrders()
    {
        return Order::all();
    }
}