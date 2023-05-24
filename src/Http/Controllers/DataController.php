<?php 

namespace Raw\Webshop\Http\Controllers;

use Webshop;

use App\Http\Controllers\Controller;

class DataController extends Controller
{
    public function getAll()
    {
        return response()->json([
            "status" => "success",
            "products" => Webshop::getProducts(),
            "orders" => Webshop::getAllOrdersForCurrentUser(),
        ]);
    }
}