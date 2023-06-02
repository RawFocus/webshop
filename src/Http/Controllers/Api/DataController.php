<?php 

namespace Raw\Webshop\Http\Controllers\Api;

use WebshopOrders;
use WebshopProducts;

use Raw\Webshop\Http\Controllers\Controller;

class DataController extends Controller
{
    public function getAll()
    {
        return response()->json([
            "status" => "success",
            "products" => WebshopProducts::getAllPreloaded(),
            "orders" => WebshopOrders::getAllPreloadedForCurrentUser(),
        ]);
    }
}