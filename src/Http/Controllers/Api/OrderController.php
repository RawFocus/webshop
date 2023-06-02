<?php 

namespace Raw\Webshop\Http\Controllers\Api;

use Exception;
use WebshopOrders;

use Raw\Webshop\Http\Controllers\Controller;

use Raw\Webshop\Http\Requests\Orders\FlagOrderShippedRequest;
use Raw\Webshop\Http\Requests\Orders\FlagOrderArrivedRequest;

use Raw\Webshop\Exceptions\Orders\OrderNotFoundException;
use Raw\Webshop\Exceptions\Orders\OrderNotYoursException;

class OrderController extends Controller
{
    public function getOrders()
    {
        return response()->json([
            "status" => "success",
            "orders" => WebshopOrders::getAll(),
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