<?php

namespace Raw\Webshop\database\seeders;

use DB;

use Raw\Webshop\Enums\OrderStatusEnum;
use Raw\Webshop\Enums\PaymentStatusEnum;

use Raw\Webshop\Models\Order;
use Raw\Webshop\Models\Product;
use Raw\Webshop\Models\ProductImage;
use Raw\Webshop\Models\ProductVariant;
use Raw\Webshop\Models\ProductVariantOption;

use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("orders")->delete();
        
        $product = Product::all()->get(0);

        $totalPrice = $product->price * 2;

        $order = Order::create([
            "user_id" => 1,
            "name" => "Lorem Ipsum",
            "email" => "test@test.nl",
            "street" => "Lorem Ipsum",
            "country" => "The Netherlands",
            "postal_code" => "1234AB",
            "city" => "Utrecht",
            "num_products" => 2,
            "total_price" => $totalPrice,
            "order_status" => OrderStatusEnum::OPEN,
            "payment_status" => PaymentStatusEnum::PAID,
            "payment_method" => "ideal",
            "payment_id" => "123456789",
        ]);

        $variants = [];
        foreach ($product->variants as $variant)
        {
            $variants[] = [
                "product_variant_id" => $variant->id,
                "product_variant_option_id" => $variant->options->get(0)->id,
            ];
        }

        $order->products()->attach($product->id, [
            "quantity" => 2,
            "variants" => $variants,
            "total_price" => $totalPrice,
        ]);
    }
}
