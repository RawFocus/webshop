<?php

namespace RawFocus\Webshop\Http\Controllers\Api;

use Log;
use Exception;
use WebshopPayments;

use RawFocus\Webshop\Http\Controllers\Controller;

use RawFocus\Webshop\Http\Requests\Checkout\CheckoutRequest;
use RawFocus\Webshop\Http\Requests\Checkout\PaymentRetryRequest;

class CheckoutController extends Controller
{
    public function postCheckout(CheckoutRequest $request)
    {
        try
        {
            $url = WebshopPayments::processCheckoutFromRequest($request);

            return response()->json([
                "status" => "success",
                "url" => $url
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
