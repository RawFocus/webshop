<?php 

namespace Raw\Webshop\Http\Controllers\Api;

use Exception;
use Raw\Webshop\Http\Controllers\Controller;
use Raw\Webshop\Http\Requests\Admin\AdminArriveOrderRequest;
use Raw\Webshop\Http\Requests\Admin\AdminDeleteProductRequest;
use Raw\Webshop\Http\Requests\Admin\AdminShipOrderRequest;
use Raw\Webshop\Http\Requests\Admin\AdminUpdateProductRequest;

class AdminController extends Controller
{
    public function postCreateProduct(AdminCreateProductRequest $request)
    {
        try
        {
            Webshop::processCreateProductFromRequest($request);
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

    public function postUpdateProduct(AdminUpdateProductRequest $request)
    {
        try
        {
            Webshop::processUpdateFromProductRequest($request);
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

    public function postDeleteProduct(AdminDeleteProductRequest $request)
    {
        try
        {
            Webshop::processDeleteProductFromRequest($request);
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

    public function postShipOrder(AdminShipOrderRequest $request)
    {
        try
        {
            Webshop::processShipOrderFromRequest($request);
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

    public function postArriveOrder(AdminArriveOrderRequest $request)
    {
        try
        {
            Webshop::processArriveOrderFromRequest($request);
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