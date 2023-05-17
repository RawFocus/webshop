<?php

namespace Tests\Unit\Services;

use Raw\Webshop\Models\Order;
use Raw\Webshop\Models\Product;
use Raw\Webshop\Http\Requests\CheckoutRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Raw\Webshop\Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testprocessCheckoutFromRequest()
    {
        $product = Product::factory()->create();
        $checkoutRequest = new CheckoutRequest([
            'products' => json_encode([
                [
                    'uuid' => $product->uuid,
                    'quantity' => 1,
                ]
            ]),
            'name' => 'Test User',
            'email' => 'test@test.com',
            'address_street' => 'Test Street',
            'address_country' => 'US',
            'address_postal_code' => '10000',
            'address_city' => 'Test City',
        ]);

        $url = Payments::processCheckoutFromRequest($checkoutRequest);

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
        $order = Order::factory()->create([
            'payment_status' => 'pending',
        ]);

        Payments::markOrderAsPaid($order);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'payment_status' => 'paid',
        ]);
    }
}
