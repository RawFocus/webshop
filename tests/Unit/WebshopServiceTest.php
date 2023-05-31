<?php

namespace Tests\Unit\Services;

use Raw\Webshop\Facades\WebshopFacade;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Raw\Webshop\database\factories\ProductFactory;
use Raw\Webshop\database\factories\OrderFactory;
use Raw\Webshop\Http\Requests\Admin\AdminCreateProductRequest;
use Raw\Webshop\Http\Requests\Admin\AdminDeleteProductRequest;
use Raw\Webshop\Http\Requests\Admin\AdminUpdateProductRequest;
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

    public function testCalculateTotalOrderPrice()
    {
        $products = [
            [
                'quantity' => 2,
                'price' => 26,
            ]
        ];

        $totalPrice = WebshopFacade::calculateTotalOrderPrice($products);

        $this->assertEquals(52, $totalPrice);
    }

    public function testProcessCreateProductFromRequest()
    {
        $request = new AdminCreateProductRequest([
            'title_nl' => 'Test product nl',
            'title_en' => 'Test product en',
            'summary_nl' => 'Test summary nl',
            'summary_en' => 'Test summary en',
            'price' => 10,
            'stock' => 5,
            'listed' => 1,
        ]);

        $product = WebshopFacade::processCreateProductFromRequest($request);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'title' => "{\"en\":\"Test product en\",\"nl\":\"Test product nl\"}",
            'summary' => "{\"en\":\"Test summary en\",\"nl\":\"Test summary nl\"}",
            'price' => 1000,
            'stock' => 5,
            'listed' => 1
        ]);
    }

    public function testProcessUpdateProductFromRequest()
    {
        $product = ProductFactory::new()->create();

        $request = new AdminUpdateProductRequest([
            'product_uuid' => $product->uuid,
            'title_nl' => 'Test product nl',
            'title_en' => 'Test product en',
            'summary_nl' => 'Test summary nl',
            'summary_en' => 'Test summary en',
            'price' => 10,
            'stock' => 5,
            'listed' => 0,
        ]);

        $product = WebshopFacade::processUpdateProductFromRequest($request);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'title' => "{\"en\":\"Test product en\",\"nl\":\"Test product nl\"}",
            'summary' => "{\"en\":\"Test summary en\",\"nl\":\"Test summary nl\"}",
            'price' => 1000,
            'stock' => 5,
            'listed' => 0,
        ]);
    }

    public function testProcessDeleteProductFromRequest()
    {
        $product = ProductFactory::new()->create();

        $request = new AdminDeleteProductRequest([
            'product_uuid' => $product->uuid,
        ]);

        WebshopFacade::processDeleteProductFromRequest($request);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }
}
