<?php 

namespace Raw\Webshop\Http\Controllers\Api;

use Webshop;
use Exception;

use Raw\Webshop\Models\Product;

use Raw\Webshop\Http\Controllers\Controller;

use Raw\Webshop\Http\Requests\Products\CreateProductRequest;
use Raw\Webshop\Http\Requests\Products\UpdateProductRequest;
use Raw\Webshop\Http\Requests\Products\DeleteProductRequest;

use Raw\Webshop\Exceptions\ProductNotFoundException;

class ProductController extends Controller
{
    public function getAll(): Collection
    {
        return response()->json([
            "status" => "success",
            "products" => Webshop::getProducts(),
        ]);
    }

    public function getFindById($id): ?Product
    {
        try
        {
            $product = Webshop::findProductById($id);

            if (!$product) throw new ProductNotFoundException(__("webshop::validation.product_not_found"));

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

    public function postCreateProduct(CreateProductRequest $request)
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

    public function postUpdateProduct(UpdateProductRequest $request)
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

    public function postDeleteProduct(DeleteProductRequest $request)
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
}