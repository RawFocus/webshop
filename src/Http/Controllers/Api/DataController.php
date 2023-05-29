<?php 

namespace Raw\Webshop\Http\Controllers\Api;

use Webshop;
use Raw\Webshop\Http\Controllers\Controller;

class DataController extends Controller
{
    public function getAll()
    {
        return response()->json([
            "status" => "success",
            "products" => Webshop::getPreloadedProducts(),
            "orders" => Webshop::getAllPreloadedOrdersForCurrentUser(),
        ]);
    }
}