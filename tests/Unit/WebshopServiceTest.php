<?php

namespace Tests\Unit\Services;

use Raw\Webshop\Facades\WebshopFacade;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Raw\Webshop\database\factories\ProductFactory;
use Raw\Webshop\database\factories\OrderFactory;
use Raw\Webshop\Tests\TestCase;

class WebshopServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testGetProducts()
    {
        ProductFactory::new()->count(3)->create();

        $products = WebshopFacade::getProducts();

        $this->assertCount(3, $products);
    }

    public function testFindProductById()
    {
        $product = ProductFactory::new()->create();

        $foundProduct = WebshopFacade::findProductById($product->id);

        $this->assertEquals($product->id, $foundProduct->id);
    }

    public function testFindProductByUuid()
    {
        $product = ProductFactory::new()->create();

        $foundProduct = WebshopFacade::findProductByUuid($product->uuid);

        $this->assertEquals($product->uuid, $foundProduct->uuid);
    }

    public function testFindProductBySlug()
    {
        $product = ProductFactory::new()->create();

        $foundProduct = WebshopFacade::findProductBySlug($product->slug);

        $this->assertEquals($product->slug, $foundProduct->slug);
    }

    public function testGetOrders()
    {
        OrderFactory::new()->count(3)->create();

        $orders = WebshopFacade::getOrders();

        $this->assertCount(3, $orders);
    }

    public function testFindOrderById()
    {
        $order = OrderFactory::new()->create();

        $foundOrder = WebshopFacade::findOrderById($order->id);

        $this->assertEquals($order->id, $foundOrder->id);
    }

    public function testRevertStockDecreases()
    {
        $product = ProductFactory::new()->create([
            'stock' => 5,
        ]);

        $order = OrderFactory::new()->create();
        $order->products()->attach($product->id, ['quantity' => 3]);

        WebshopFacade::revertStockDecreases($order);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 8,
        ]);
    }
}
