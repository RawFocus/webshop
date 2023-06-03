<?php

namespace Raw\Webshop\Services;

use WebshopProducts;

use Raw\Webshop\Models\Order;

use Raw\Webshop\Enums\OrderStatusEnum;

use Raw\Webshop\Http\Requests\Orders\FlagAsShippedRequest;
use Raw\Webshop\Http\Requests\Orders\FlagAsArrivedRequest;

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
        $order->products = $order->products->map(function($product) {
            return WebshopProducts::preload($product);
        });

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