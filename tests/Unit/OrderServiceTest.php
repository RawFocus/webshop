<?php

namespace Tests\Unit\Services;

use Raw\Webshop\Models\Order;

use Raw\Webshop\Tests\TestCase;

use Raw\Webshop\Facades\WebshopOrdersFacade;

use Raw\Webshop\database\factories\OrderFactory;
use Raw\Webshop\database\factories\ProductFactory;

use Raw\Webshop\Http\Requests\Orders\FlagAsArrivedRequest;
use Raw\Webshop\Http\Requests\Orders\FlagAsShippedRequest;

use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private function assertIsPreloaded(Order $order)
    {
        
    }

    public function testGetAll()
    {
        OrderFactory::new()->count(3)->create();

        $results = WebshopOrdersFacade::getAll();
        $this->assertInstanceOf(Order::class, $results->get(0));
        $this->assertCount(3, $results);
    }

    public function testGetAllPreloaded()
    {
        OrderFactory::new()->count(3)->create();

        $results = WebshopOrdersFacade::getAllPreloaded();
        $this->assertInstanceOf(Order::class, $results->get(0));
        $this->assertCount(3, $results);
    }

    public function testGetOrdersPreloadedForCurrentUser()
    {
        OrderFactory::new()->count(3)->create();

        $results = WebshopOrdersFacade::getAllPreloadedForCurrentUser();
        $this->assertInstanceOf(Order::class, $results->get(0));
        $this->assertCount(3, $results);
    }

    public function testFindOrderById()
    {
        $order = OrderFactory::new()->create();

        $result = WebshopOrdersFacade::findOrderById($order->id);
        $this->assertEquals($order->id, $result->id);
    }

    public function testFindOrderByUuid()
    {
        $order = OrderFactory::new()->create();

        $result = WebshopOrdersFacade::findOrderByUuid($order->uuid);
        $this->assertEquals($order->id, $result->id);
    }

    public function testCalculateTotalOrderPrice()
    {
        $products = [
            [
                'quantity' => 2,
                'price' => 26,
            ],
        ];

        $totalPrice = WebshopOrdersFacade::calculateTotalOrderPrice($products);

        $this->assertEquals(52, $totalPrice);
    }

    public function testRevertStockDecreases()
    {
        $product = ProductFactory::new()->create([
            'stock' => 5,
        ]);

        $order = OrderFactory::new()->create();
        $order->products()->attach($product->id, ['quantity' => 3]);

        WebshopOrdersFacade::revertStockDecreases($order);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 8,
        ]);
    }

    public function testProcessFlagAsArrivedRequest()
    {
        $product = ProductFactory::new()->create();

        $request = new FlagAsArrivedRequest([
            "uuid" => $product->uuid,
        ]);

        $result = WebshopOrdersFacade::processFlagAsArrivedRequest($request);
        $this->assertInstanceOf(Product::class, $result);
    }

    public function testProcessFlagAsShippedRequest()
    {
        $product = ProductFactory::new()->create();

        $request = new FlagAsShippedRequest([
            "uuid" => $product->uuid,
        ]);

        $result = WebshopOrdersFacade::processFlagAsShippedRequest($request);
        $this->assertInstanceOf(Product::class, $result);
    }
}
