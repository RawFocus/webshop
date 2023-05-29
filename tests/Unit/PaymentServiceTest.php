<?php

namespace Tests\Unit\Services;

use Mockery;
use Raw\Webshop\Http\Requests\CheckoutRequest;
use Raw\Webshop\database\factories\ProductFactory;
use Raw\Webshop\database\factories\OrderFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Raw\Webshop\Facades\PaymentsFacade;
use Raw\Webshop\Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testprocessCheckoutFromRequest()
    {

        // Create a mock user object
        $mockUser = new \stdClass;
        $mockUser->id = 1;
        $mockUser->name = 'John Doe';
        $mockUser->email = 'john@example.com';

        // Create a mock guard
        $mockGuard = Mockery::mock();
        $mockGuard->shouldReceive('user')->andReturn($mockUser);

        // Create a mock auth manager
        $mockAuth = Mockery::mock('Illuminate\Auth\AuthManager');
        $mockAuth->shouldReceive('guard')->with('sanctum')->andReturn($mockGuard);

        // Register the mock auth manager
        $this->app->instance('auth', $mockAuth);

        $product = ProductFactory::new()->create();
        $checkoutRequest = new CheckoutRequest([
            'products' => [
                [
                    'uuid' => $product->uuid,
                    'quantity' => 1,
                    'price' => 26,
                ]
            ],
            'name' => 'Test User',
            'email' => 'test@test.com',
            'address_street' => 'Test Street',
            'address_country' => 'US',
            'address_postal_code' => '10000',
            'address_city' => 'Test City',
        ]);

        $url = PaymentsFacade::processCheckoutFromRequest($checkoutRequest);

        $this->assertNotNull($url);
        $this->assertDatabaseHas('orders', [
            'name' => 'Test User',
            'email' => 'test@test.com',
            'address_street' => 'Test Street',
            'address_country' => 'US',
            'address_postal_code' => '10000',
            'address_city' => 'Test City',
        ]);
        $this->assertDatabaseHas('order_product', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
    }

    public function testMarkOrderAsPaid()
    {
        $order = OrderFactory::new()->create([
            'payment_status' => 'pending',
        ]);

        PaymentsFacade::markOrderAsPaid($order);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'payment_status' => 'paid',
        ]);
    }
}
