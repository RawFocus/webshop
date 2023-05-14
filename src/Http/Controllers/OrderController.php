<?php 

namespace Raw\Webshop\Http\Controllers;

use Webshop;
use Exception;

use App\Http\Controllers\Controller;

use Raw\Webshop\Exceptions\OrderNotFoundException;

class OrderController extends Controller
{
    public function getOrders()
    {
        return response()->json([
            "status" => "success",
            "orders" => Webshop::getAll(),
        ]);
    }

    public function getFindOrderById($id)
    {
        try
        {
            $order = Webshop::findOrderById($id);
    
            if (!$order) throw new OrderNotFoundException(__("webshop::validation.order_not_found"));
    
            return response()->json([
                "status" => "success",
                "order" => $order,
            ]);
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
            $order = Webshop::findOrderByUuid($uuid);
    
            if (!$order) throw new OrderNotFoundException(__("webshop::validation.order_not_found"));
    
            return response()->json([
                "status" => "success",
                "order" => $order,
            ]);
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
}