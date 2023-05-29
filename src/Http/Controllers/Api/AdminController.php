<?php 

namespace Raw\Webshop\Http\Controllers\Api;

use Exception;
use Raw\Webshop\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function postCreate(CheckoutRequest $request)
    {
        try
        {
            // TODO: create product
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