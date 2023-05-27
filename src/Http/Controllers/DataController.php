<?php 

namespace Raw\Webshop\Http\Controllers;

use Webshop;

use App\Http\Controllers\Controller;

class DataController extends Controller
{
    public function getAll()
    {
        $user = auth()->user();
        return response()->json([
            "status" => "success",
            "products" => Webshop::getPreloadedProducts(),
            "orders" => Webshop::getAllOrdersForCurrentUser($user),
        ]);
    }
}