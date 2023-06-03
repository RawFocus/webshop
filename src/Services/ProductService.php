<?php

namespace Raw\Webshop\Services;

use Raw\Webshop\Models\Product;
use Raw\Webshop\Models\ProductVariant;
use Raw\Webshop\Models\ProductVariantOption;

use Raw\Webshop\Enums\ProductTypeEnum;

use Raw\Webshop\Http\Requests\Products\CreateProductRequest;
use Raw\Webshop\Http\Requests\Products\UpdateProductRequest;
use Raw\Webshop\Http\Requests\Products\DeleteProductRequest;

use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    /**
     * Preload
     * 
     * @param       Product     $product
     * @return      Product
     */
    public function preload(Product $product): Product
    {
        // Preload images
        $product->images = $product->images->map(function($image) {
            $image->path = asset($image->path);
            return $image;
        });

        // Preload variants
        $product->variants = $product->variants->map(function ($variant) {
            return $this->preloadProductVariant($variant);
        });

        // Preload type label
        $product->type_label = ProductTypeEnum::labelFromValue($product->type);

        return $product;
    }

    public function preloadProductVariant(ProductVariant $variant)
    {
        $variant->load("options");

        return $variant;
    }

    /**
     * Get products
     * 
     * @return      Collection
     */
    public function getAll(): Collection
    {
        return Product::all();
    }

    /**
     * Get products preloaded
     * 
     * @return      Collection
     */
    public function getAllPreloaded(): Collection
    {
        return Product::all()->map(function ($product) {
            return $this->preload($product);
        });
    }
    
    /**
     * Find product by id
     * 
     * @param       int         $id
     * @return      Product
     */
    public function findById(int $id): ?Product
    {
        return Product::find($id);
    }

    /**
     * Find product by uuid
     * 
     * @param       string      $uuid
     * @return      Product
     */
    public function findByUuid(string $uuid): ?Product
    {
        return Product::where("uuid", $uuid)->first();
    }

    /**
     * Find product by slug
     * 
     * @param       string      $slug
     * @return      Product
     */
    public function findBySlug(string $slug): ?Product
    {
        return Product::where("slug", $slug)->first();
    }

    /**
     * Process create product from request
     * 
     * @param       CreateProductRequest       $request
     * @return      Product
     */
    public function processCreateProductFromRequest(CreateProductRequest $request)
    {
        return Product::create([
            "title" => [
                "en" => $request->title_en,
                "nl" => $request->title_nl,
            ],
            "summary" => [
                "en" => $request->summary_en,
                "nl" => $request->summary_nl,
            ],
            "price" => $request->price,
            "stock" => $request->stock,
            "listed" => $request->listed,
        ]);
    }

    /**
     * Process update product from request
     * 
     * @param       UpdateProductRequest       $request
     * @return      Product
     */
    public function processUpdateProductFromRequest(UpdateProductRequest $request)
    {
        $product = $this->findByUuid($request->product_uuid);
        $product->title = [
            "en" => $request->title_en,
            "nl" => $request->title_nl,
        ];
        $product->summary = [
            "en" => $request->summary_en,
            "nl" => $request->summary_nl,
        ];
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->listed = $request->listed;
        $product->save();

        return $product->refresh();
    }

    /**
     * Process delete product from request
     * 
     * @param       DeleteProductRequest       $request
     * @return      void
     */
    public function processDeleteProductFromRequest(DeleteProductRequest $request)
    {
        $product = $this->findByUuid($request->product_uuid);
        $product->delete();
    }
}