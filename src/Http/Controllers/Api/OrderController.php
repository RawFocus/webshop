<?php 

namespace RawFocus\Webshop\Http\Controllers\Api;

use Exception;
use WebshopOrders;

use RawFocus\Webshop\Http\Controllers\Controller;

use RawFocus\Webshop\Http\Requests\Orders\FlagOrderShippedRequest;
use RawFocus\Webshop\Http\Requests\Orders\FlagOrderArrivedRequest;

use RawFocus\Webshop\Exceptions\Orders\OrderNotFoundException;
use RawFocus\Webshop\Exceptions\Orders\OrderNotYoursException;

class OrderController extends Controller
{
    public function getAll()
    {
        return response()->json([
            "status" => "success",
            "orders" => WebshopOrders::getAllPreloaded(),
        ]);
    }

    public function getFindOrderById($id)
    {
        try
        {
            $user = auth("sanctum")->user();

            $order = WebshopOrders::findOrderById($id);

            if ($order->user_id != $user->id) throw new OrderNotYoursException(__("webshop::validation.order_not_found"));
    
            if (!$order) throw new OrderNotFoundException(__("webshop::validation.order_not_found"));
    
            return response()->json([
                "status" => "success",
                "order" => $order,
            ]);
        }
        catch (OrderNotYoursException $e)
        {
            return response()->json([
                "status" => "error",
                "error" => $e->getMessage()
            ], 403);
        }
        catch (OrderNotFoundException $e)
        {
            return response()->json([
                "status" => "error",
                "error" => $e->getMessage()
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

    public function getFindOrderByUuid($uuid)
    {
        try
        {
            $user = auth("sanctum")->user();

            $order = WebshopOrders::findOrderByUuid($uuid);
    
            if (!$order) throw new OrderNotFoundException(__("webshop::validation.order_not_found"));

            if ($order->user_id != $user->id) throw new OrderNotYoursException(__("webshop::validation.order_not_found"));
    
            return response()->json([
                "status" => "success",
                "order" => $order,
            ]);
        }
        catch (OrderNotYoursException $e)
        {
            return response()->json([
                "status" => "error",
                "error" => $e->getMessage()
            ], 403);
        }
        catch (OrderNotFoundException $e)
        {
            return response()->json([
                "status" => "error",
                "error" => $e->getMessage()
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
    
    public function postFlagOrderShipped(FlagOrderShippedRequest $request)
    {
        try
        {
            WebshopOrders::processShipOrderFromRequest($request);
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

    public function postFlagOrderArrived(FlagOrderArrivedRequest $request)
    {
        try
        {
            WebshopOrders::processArriveOrderFromRequest($request);
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