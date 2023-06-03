<?php

namespace Raw\Webshop\Database\Seeders;

use DB;

use Raw\Webshop\Models\Product;
use Raw\Webshop\Models\ProductImage;
use Raw\Webshop\Models\ProductVariant;
use Raw\Webshop\Models\ProductVariantOption;

use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("products")->delete();

        for ($i = 0; $i < 10; $i++)
        {
            $product = Product::create([
                "type" => rand(0,1) == 0 ? "tshirt" : "hoodie",
                "title" => [
                    "nl" => "Product",
                    "en" => "Product"
                ],
                "summary" => [
                    "nl" => "Summary",
                    "en" => "Summary"
                ],
                "price" => 26,
                "stock" => 10,
                "listed" => true
            ]);

            $size = ProductVariant::create([
                "product_id" => $product->id,
                "name" => [
                    "en" => "Size",
                    "nl" => "Maat"
                ],
            ]);

            ProductVariantOption::create([
                "product_variant_id" => $size->id,
                "name" => [
                    "en" => "S",
                    "nl" => "S"
                ],
            ]);
            ProductVariantOption::create([
                "product_variant_id" => $size->id,
                "name" => [
                    "en" => "M",
                    "nl" => "M"
                ],
            ]);
            ProductVariantOption::create([
                "product_variant_id" => $size->id,
                "name" => [
                    "en" => "L",
                    "nl" => "L"
                ],
            ]);
            ProductVariantOption::create([
                "product_variant_id" => $size->id,
                "name" => [
                    "en" => "XL",
                    "nl" => "XL"
                ],
            ]);
            ProductVariantOption::create([
                "product_variant_id" => $size->id,
                "name" => [
                    "en" => "XXL",
                    "nl" => "XXL"
                ],
            ]);
            ProductVariantOption::create([
                "product_variant_id" => $size->id,
                "name" => [
                    "en" => "3XL",
                    "nl" => "3XL"
                ],
            ]);
    
            for ($j = 0; $j < 5; $j++)
            {
                ProductImage::create([
                    "product_id" => $product->id,
                    "path" => "/storage/images/webshop/products/default.jpg"
                ]);
            }

        }
    }
}
