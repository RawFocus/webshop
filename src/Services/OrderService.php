<?php

namespace RawFocus\Webshop\Services;

use WebshopProducts;

use RawFocus\Webshop\Models\Order;
use RawFocus\Webshop\Models\ProductVariant;
use RawFocus\Webshop\Models\ProductVariantOption;

use RawFocus\Webshop\Enums\OrderStatusEnum;
use RawFocus\Webshop\Enums\PaymentStatusEnum;

use RawFocus\Webshop\Http\Requests\Checkout\CheckoutRequest;
use RawFocus\Webshop\Http\Requests\Orders\FlagAsShippedRequest;
use RawFocus\Webshop\Http\Requests\Orders\FlagAsArrivedRequest;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class OrderService
{
    /**
     * Preload order
     * 
     * @param       Order       $order
     * @return      Order       $order
     */
    public function preload(Order $order): Order
    {
        // Preload the products
        $order->products = $order->products->map(function($product) {
            
            $product = WebshopProducts::preload($product);

            $selectedVariants = [];
            foreach ($product->pivot->variants as $variant)
            {
                $v = ProductVariant::find($variant->product_variant_id);
                $o = ProductVariantOption::find($variant->product_variant_option_id);

                $selectedVariants[] = [
                    "variant" => $v->name,
                    "option" => $o->name,
                ];
            }
            $product->selected_variants = $selectedVariants;
            
            return $product;

        });

        // Preload the order & payment status labels
        $order->order_status_label = OrderStatusEnum::labelFromValue($order->order_status->value);
        $order->payment_status_label = PaymentStatusEnum::labelFromValue($order->payment_status->value);

        // Format the date
        $order->formatted_created_at = $order->created_at->format("d-m-Y H:i:s");
        $order->formatted_updated_at = $order->updated_at->format("d-m-Y H:i:s");

        // Return updated order
        return $order;
    }

    /**
     * Get orders
     * 
     * @return      Collection
     */
    public function getAll(): Collection
    {
        return Order::all();
    }

    /**
     * Get orders preloaded
     * 
     * @return      Collection
     */
    public function getAllPreloaded(): Collection
    {
        return $this->getAll()->map(function($order) {
            return $this->preload($order);
        });
    }

    /**
     * Get orders preloaded for current user
     * 
     * @return      SupportCollection
     */
    public function getAllPreloadedForCurrentUser(): SupportCollection
    {
        $user = auth("sanctum")->user();
        
        if (!$user) return collect([]);

        return Order::where("user_id", $user->id)
            ->get()
            ->map(function($order) {
                return $this->preload($order);
            });
    }
    
    /**
     * Find order by id
     * 
     * @param       int                         $id
     * @return      Order|null
     */
    public function findOrderById(int $id): ?Order
    {
        return Order::find($id);
    }

    /**
     * Find order by uuid
     * 
     * @param       string                      $uuid
     * @return      Order|null
     */
    public function findOrderByUuid(string $uuid): ?Order
    {
        return Order::where("uuid", $uuid)->first();
    }
    
    /**
     * Create order from request
     * 
     * @param       CheckoutRequest              $request
     * @return      Order
     */
    public function createFromCheckoutRequest(CheckoutRequest $request): Order
    {
        // Grab the logged in user
        $user = auth("sanctum")->user();

        // Create order
        $order = Order::create([
            "name" => $user->name,
            "email" => $user->email,
            "user_id" => $user->id,
            "street" => $request->street,
            "postal_code" => $request->postal_code,
            "city" => $request->city,
            "country" => $request->country,
            "num_products" => $this->calculateTotalNumberOfProduct($request->products),
            "total_price" => $this->calculateTotalOrderPrice($request->products),
        ]);

        // Attach products
        foreach ($request->products as $productData)
        {
            // Fetch product
            $product = WebshopProducts::findByUuid($productData["product"]["uuid"]);

            // Generate the array of variants to save on the pivot table
            $variants = [];
            foreach ($productData["variants"] as $variant)
            {
                $variants[] = [
                    "product_variant_id" => $variant["variant_id"],
                    "product_variant_option_id" => $variant["variant_option_id"],
                ];
            }

            // Attach product to order
            $order->products()->attach($product->id, [
                'quantity' => $productData["quantity"],
                'variants' => $variants,
                'total_price' => $product->price * $productData["quantity"],
            ]);

            // Decrease stock from product
            $product->stock -= $productData["quantity"];
            $product->save();
        }

        // Return refreshed user
        return $order->refresh();
    }

    /**
     * Calculate total number of product
     * 
     * @param       array                       $productData
     * @return      int
     */
    public function calculateTotalNumberOfProduct(array $productData): int
    {
        $out = 0;
        foreach ($productData as $productItem)
        {
            $out += $productItem["quantity"];
        }  

        return $out;     
    }

    /**
     * Calculate total order price
     * 
     * @param       array                       $productData
     * @return      float
     */
    public function calculateTotalOrderPrice(array $productData): float
    {
        $out = 0;
        foreach ($productData as $productItem)
        {
            $out += $productItem["product"]["price"] * $productItem["quantity"];
        }  

        return $out;     
    }

    /**
     * Revert stock decreases
     * 
     * @param       Order                       $order
     * @return      void
     */
    public function revertStockDecreases(Order $order)
    {
        foreach ($order->products as $product)
        {
            $product->stock += $product->pivot->quantity;
            $product->save();
        }
    }

    /**
     * Process flag as arrived request
     * 
     * @param       FlagAsArrivedRequest        $request
     * @return      Order
     */
    public function processFlagAsArrivedRequest(FlagAsArrivedRequest $request): Order
    {
        $order = $this->findOrderByUuid($request->uuid);
        $order->order_status = OrderStatusEnum::ARRIVED;
        $order->save();

        return $order;
    }

    /**
     * Process flag as shipped request
     * 
     * @param       FlagAsShippedRequest        $request
     * @return      Order
     */
    public function processFlagAsShippedRequest(FlagAsShippedRequest $request): Order
    {
        $order = $this->findOrderByUuid($request->uuid);
        $order->order_status = OrderStatusEnum::SHIPPED;
        $order->save();

        return $order;
    }
}