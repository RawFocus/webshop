<?php 

namespace Raw\Webshop\Http\Controllers;

use Webshop;
use Exception;

use App\Http\Controllers\Controller;

class CheckoutController extends Controller
{
    public function postCreate(CheckoutRequest $request)
    {
        try
        {
            Webshop::checkoutFromRequest($request);

            return response()->json([
                "status" => "success",
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