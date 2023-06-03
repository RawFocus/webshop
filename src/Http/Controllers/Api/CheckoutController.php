<?php

namespace Raw\Webshop\Http\Controllers\Api;

use Log;
use Payments;
use Exception;

use Raw\Webshop\Http\Controllers\Controller;

use Raw\Webshop\Http\Requests\Checkout\CheckoutRequest;
use Raw\Webshop\Http\Requests\Checkout\PaymentRetryRequest;

class CheckoutController extends Controller
{
    public function postCheckout(CheckoutRequest $request)
    {
        dd("hello");

        // try
        // {
        //     $url = WebshopPayments::processCheckoutFromRequest($request);

        //     return response()->json([
        //         "status" => "success",
        //         "url" => $url
        //     ]);
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

    public function postPaymentRetry(PaymentRetryRequest $request)
    {
        try
        {
            $url = WebshopPayments::processPaymentRetryFromRequest($request);

            return response()->json([
                "status" => "success",
                "url" => $url,
            ]);
        }
        catch (Exception $e)
        {
            Log::error($e);
            return response()->json([
                "status" => "error",
                "error" => __("webshop::validation.general_error")
            ]);
        }
    }
}
