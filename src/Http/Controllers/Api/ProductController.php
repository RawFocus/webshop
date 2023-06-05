<?php

namespace RawFocus\Webshop\Http\Controllers\Api;

use Exception;

use RawFocus\Webshop\Models\Product;

use RawFocus\Webshop\Facades\WebshopProducts;

use RawFocus\Webshop\Http\Controllers\Controller;

use RawFocus\Webshop\Http\Requests\Products\CreateProductRequest;
use RawFocus\Webshop\Http\Requests\Products\UpdateProductRequest;
use RawFocus\Webshop\Http\Requests\Products\DeleteProductRequest;

use RawFocus\Webshop\Exceptions\Products\ProductNotFoundException;

class ProductController extends Controller
{
    public function getAll(): Collection
    {
        return response()->json([
            "status" => "success",
            "products" => WebshopProducts::getAllPreloaded(),
        ]);
    }

    public function getFindById($id): ?Product
    {
        try
        {
            $product = WebshopProducts::findById($id);

            if (!$product) throw new ProductNotFoundException(__("webshop::validation.product_not_found"));

            return response()->json([
                "status" => "success",
                "product" => WebshopProducts::preload($product),
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
            $product = WebshopProducts::findBySlug($slug);

            if (!$product) throw new ProductNotFoundException(__("validation.product_not_found"));

            return response()->json([
                "status" => "success",
                "product" => WebshopProducts::preload($product),
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
            $product = WebshopProducts::processCreateProductFromRequest($request);

            return response()->json([
                "status" => "success",
                "product" => WebshopProducts::preload($product),
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
            $product = WebshopProducts::processUpdateFromProductRequest($request);

            return response()->json([
                "status" => "success",
                "product" => WebshopProducts::preload($product),
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
            WebshopProducts::processDeleteProductFromRequest($request);

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