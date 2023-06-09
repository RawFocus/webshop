<?php

namespace Tests\Unit\Services;

use RawFocus\Webshop\Models\Product;

use RawFocus\Webshop\Tests\TestCase;

use RawFocus\Webshop\Facades\WebshopOrdersFacade;
use RawFocus\Webshop\Facades\WebshopProductsFacade;

use RawFocus\Webshop\database\factories\ProductFactory;

use RawFocus\Webshop\Http\Requests\Products\CreateProductRequest;
use RawFocus\Webshop\Http\Requests\Products\DeleteProductRequest;
use RawFocus\Webshop\Http\Requests\Products\UpdateProductRequest;

use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    private function assertIsPreloaded(Product $product)
    {
        
    }

    public function testPreload()
    {
        $product = ProductFactory::new()->create();

        $result = WebshopProductsFacade::preload($product);
        $this->assertIsPreloaded($result);
    }

    public function testGetProducts()
    {
        ProductFactory::new()->count(3)->create();

        $results = WebshopProductsFacade::getAll();
        $this->assertCount(3, $results);
        $this->assertInstanceOf(Product::class, $results->get(0));
    }

    public function testGetProductsPreloaded()
    {
        ProductFactory::new()->count(3)->create();

        $results = WebshopProductsFacade::getAllPreloaded();
        $this->assertCount(3, $results);
        $this->assertInstanceOf(Product::class, $results->get(0));
        $this->assertIsPreloaded($results->get(0));
    }

    public function testFindById()
    {
        $product = ProductFactory::new()->create();

        $result = WebshopProductsFacade::findById($product->id);
        $this->assertInstanceOf(Product::class, $result);
        $this->assertEquals($product->id, $result->id);
    }

    public function testFindByUuid()
    {
        $product = ProductFactory::new()->create();

        $result = WebshopProductsFacade::findByUuid($product->uuid);
        $this->assertInstanceOf(Product::class, $result);
        $this->assertEquals($product->uuid, $result->uuid);
    }

    public function testFindBySlug()
    {
        $product = ProductFactory::new()->create();

        $result = WebshopProductsFacade::findBySlug($product->slug);
        $this->assertInstanceOf(Product::class, $result);
        $this->assertEquals($product->slug, $result->slug);
    }

    public function testProcessCreateProductFromRequest()
    {
        $request = new CreateProductRequest([
            'title_nl' => 'Test product nl',
            'title_en' => 'Test product en',
            'summary_nl' => 'Test summary nl',
            'summary_en' => 'Test summary en',
            'price' => 10,
            'stock' => 5,
            'listed' => 1,
        ]);

        $result = WebshopProductsFacade::processCreateProductFromRequest($request);
        $this->assertInstanceOf(Product::class, $result);

        $this->assertDatabaseHas('products', [
            'id' => $result->id,
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

        $request = new UpdateProductRequest([
            'product_uuid' => $product->uuid,
            'title_nl' => 'Test product nl',
            'title_en' => 'Test product en',
            'summary_nl' => 'Test summary nl',
            'summary_en' => 'Test summary en',
            'price' => 10,
            'stock' => 5,
            'listed' => 0,
        ]);

        $result = WebshopProductsFacade::processUpdateProductFromRequest($request);
        $this->assertInstanceOf(Product::class, $result);

        $this->assertDatabaseHas('products', [
            'id' => $result->id,
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

        $request = new DeleteProductRequest([
            'product_uuid' => $product->uuid,
        ]);

        WebshopProductsFacade::processDeleteProductFromRequest($request);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }
}
