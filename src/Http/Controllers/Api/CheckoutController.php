<?php

namespace Raw\Webshop\Http\Controllers\Api;

use Log;
use Payments;
use Exception;

use Raw\Webshop\Http\Controllers\Controller;
use Raw\Webshop\Http\Requests\CheckoutRequest;
use Raw\Webshop\Http\Requests\PaymentRetryRequest;

class CheckoutController extends Controller
{
    public function postCheckout(CheckoutRequest $request)
    {
        try
        {
            return response()->json([
                "status" => "success",
                "url" => Payments::processCheckoutFromRequest($request)
            ]);
        }
        catch (Exception $e)
        {
            Log::error($e);
            return response()->json([
                "status" => "error",
                "error" => __("webshop.validation.general_error")
            ]);
        }
    }

    public function postPaymentRetry(PaymentRetryRequest $request)
    {
        try
        {
            return response()->json([
                "status" => "success",
                "url" => Payments::processPaymentRetryFromRequest($request)
            ]);
        }
        catch (Exception $e)
        {
            Log::error($e);
            return response()->json([
                "status" => "error",
                "error" => __("webshop.validation.general_error")
            ]);
        }
    }
}
