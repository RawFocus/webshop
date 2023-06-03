<?php

namespace Tests\Unit\Services;

use Mockery;
use Raw\Webshop\database\factories\ProductFactory;
use Raw\Webshop\database\factories\OrderFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Raw\Webshop\Facades\WebshopPaymentsFacade;
use Raw\Webshop\Http\Requests\Checkout\CheckoutRequest;
use Raw\Webshop\Models\Order;
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

        // Create new product
        $product = ProductFactory::new()->create([
            "title" => [
                "nl" => "Test product dutch",
                "en" => "Test product english",
            ],
            "stock" => 10,
        ]);
        $checkoutRequest = new CheckoutRequest([
            'products' => [
                [
                    'uuid' => $product->uuid,
                    'quantity' => 2,
                    'price' => 26,
                ]
            ],
            'address' => 'Test Street',
            'address_country' => 'NL',
            'address_postal_code' => '1234 AA',
            'address_city' => 'Test City',
        ]);

        // Test the method
        $url = WebshopPaymentsFacade::processCheckoutFromRequest($checkoutRequest);

        $this->assertNotNull($url);
        $this->assertDatabaseHas('orders', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'address_street' => 'Test Street',
            'address_country' => 'NL',
            'address_postal_code' => '1234 AA',
            'address_city' => 'Test City',
            'total_price' => 5200,
        ]);
        $this->assertDatabaseHas('order_product', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 8,
        ]);
        $order = Order::first();

        $sessionData = [
            "client_reference_id" => $order->uuid,
            "success_url" => config("webshop.payments.urls.success") . "/" . $order->uuid,
            "cancel_url" => config("webshop.payments.urls.cancel") . "/" . $order->uuid,
            "payment_method_types" => [config("webshop.payments.payment_method_types")],
            "mode" => "payment",
            "metadata" => [
                "source" => env("APP_ENV"),
            ]
        ];

        // Add products to session
        $sessionData["line_items"][] = [
            "price_data" => [
                // Stripe only accepts lowercase for currency
                "currency" => "eur",
                "product_data" => [
                    "name" => "Test product dutch"
                ],
                // Stripe api handles 10,00 like 1000. Hence, why the value is multiplied by 100
                "unit_amount_decimal" => 2600
            ],
            "quantity" => 2,
            "tax_rates" => [config("webshop.payments.tax_rates.high")],
        ];

        $this->assertEquals(
           $sessionData,
            $this->stripeClient->getParamsByType("sessions")
        );
    }

    public function testMarkOrderAsPaid()
    {
        $order = OrderFactory::new()->create([
            'payment_status' => 'pending',
        ]);

        WebshopPaymentsFacade::markOrderAsPaid($order);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'payment_status' => 'paid',
        ]);
    }
}
