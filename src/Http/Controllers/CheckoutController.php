<?php 

namespace Raw\Webshop\Http\Controllers;

use Webshop;
use Exception;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;

class CheckoutController extends Controller
{
    public function postCheckout(CheckoutRequest $request)
    {
        try
        {
            return response()->json([
                "status" => "success",
                "url" => Webshop::checkoutFromRequest($request)
            ]);
        }
        catch (Exception $e)
        {
            return response()->json([
                "status" => "error",
                "error" => __("webshop::validation.general_error")
            ]);
        }
    }
}