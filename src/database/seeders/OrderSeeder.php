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

        $order = Order::create([
            "user_id" => 1,
            "name" => "Lorem Ipsum",
            "email" => "test@test.nl",
            "street" => "Lorem Ipsum",
            "country" => "The Netherlands",
            "postal_code" => "1234AB",
            "city" => "Utrecht",
            "total_price" => 100,
            "order_status" => OrderStatusEnum::OPEN,
            "payment_status" => PaymentStatusEnum::PAID,
            "payment_method" => "ideal",
            "payment_id" => "123456789",
        ]);

        $product = Product::all()->get(0);

        $variants = [];
        foreach ($product->variants as $variant)
        {
            $variants[] = [
                "product_variant_id" => $variant->id,
                "product_variant_option_id" => $variant->options->get(0)->id,
            ];
        }

        $order->products()->attach($product->id, [
            "quantity" => 1,
            "variants" => json_encode($variants)
        ]);
    }
}
