<?php 

namespace Raw\Webshop\Http\Controllers;

use Webshop;
use Exception;

use Raw\Webshop\Models\Product;
use Raw\Webshop\Exceptions\ProductNotFoundException;

use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function getAll(): Collection
    {
        return response()->json([
            "status" => "success",
            "products" => Webshop::getAllProducts(),
        ]);
    }

    public function getFindById($id): ?Product
    {
        try
        {
            $product = Webshop::findProductById($id);

            if (!$product) throw new ProductNotFoundException(__("validation.product_not_found"));

            return response()->json([
                "status" => "success",
                "product" => $product,
            ]);
        }
        catch (Exception $e)
        {
            return response()->json([
                "status" => "error",
                "error" => __("webshop::validation.general_error")
            ]);
        }
        catch (ProductNotFoundException $e)
        {
            return response()->json([
                "status" => "error",
                "error" => __("webshop::validation.product_not_found")
            ]);
        }
    }

    public function getFindBySlug($slug): ?Product
    {
        try
        {
            $product = Webshop::findProductBySlug($slug);

            if (!$product) throw new ProductNotFoundException(__("validation.product_not_found"));

            return response()->json([
                "status" => "success",
                "product" => $product,
            ]);
        }
        catch (Exception $e)
        {
            return response()->json([
                "status" => "error",
                "error" => __("webshop::validation.general_error")
            ]);
        }
        catch (ProductNotFoundException $e)
        {
            return response()->json([
                "status" => "error",
                "error" => __("webshop::validation.product_not_found")
            ]);
        }
    }
}