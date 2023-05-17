<?php

namespace Tests\Unit\Services;

use Raw\Webshop\Facades\Webshop;
use Raw\Webshop\Models\Order;
use Raw\Webshop\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Raw\Webshop\Tests\TestCase;

class WebshopServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testGetProducts()
    {
        Product::factory()->count(3)->create();

        $products = Webshop::getProducts();

        $this->assertCount(3, $products);
    }

    public function testFindProductById()
    {
        $product = Product::factory()->create();

        $foundProduct = Webshop::findProductById($product->id);

        $this->assertEquals($product->id, $foundProduct->id);
    }

    public function testFindProductByUuid()
    {
        $product = Product::factory()->create();

        $foundProduct = Webshop::findProductByUuid($product->uuid);

        $this->assertEquals($product->uuid, $foundProduct->uuid);
    }

    public function testFindProductBySlug()
    {
        $product = Product::factory()->create();

        $foundProduct = Webshop::findProductBySlug($product->slug);

        $this->assertEquals($product->slug, $foundProduct->slug);
    }

    public function testGetOrders()
    {
        Order::factory()->count(3)->create();

        $orders = Webshop::getOrders();

        $this->assertCount(3, $orders);
    }

    public function testFindOrderById()
    {
        $order = Order::factory()->create();

        $foundOrder = Webshop::findOrderById($order->id);

        $this->assertEquals($order->id, $foundOrder->id);
    }

    public function testFindPRoductByUuidzs()
    {
        $order = Order::factory()->create();

        $foundOrder = Webshop::findOrderByUuid($order->uuid);

        $this->assertEquals($order->uuid, $foundOrder->uuid);
    }

    public function testRevertStockDecreases()
    {
        $product = Product::factory()->create([
            'stock' => 5,
        ]);

        $order = Order::factory()->create();
        $order->products()->attach($product->id, ['quantity' => 3]);

        Webshop::revertStockDecreases($order);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 8,
        ]);
    }
}
