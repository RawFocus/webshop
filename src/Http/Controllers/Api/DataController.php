<?php 

namespace RawFocus\Webshop\Http\Controllers\Api;

use WebshopOrders;
use WebshopProducts;

use RawFocus\Webshop\Http\Controllers\Controller;

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