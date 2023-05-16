<?php

namespace Raw\Webshop\Http\Controllers;

use Log;
use Payments;
use Exception;

use App\Http\Controllers\Controller;
use Raw\Webshop\Http\Requests\CheckoutRequest;

class CheckoutController extends Controller
{
    public function postCheckout(CheckoutRequest $request)
    {
        // try
        // {
            return response()->json([
                "status" => "success",
                "url" => Payments::checkoutFromRequest($request)
            ]);
        // }
        // catch (Exception $e)
        // {
        //     Log::error($e);
        //     return response()->json([
        //         "status" => "error",
        //         "error" => __("webshop::validation.general_error")
        //     ]);
        // }
    }
}
