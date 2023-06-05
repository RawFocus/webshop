<?php

namespace RawFocus\Webshop\database\seeders;

use DB;

use RawFocus\Webshop\Models\Product;
use RawFocus\Webshop\Models\ProductImage;
use RawFocus\Webshop\Models\ProductVariant;
use RawFocus\Webshop\Models\ProductVariantOption;

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
            // Generate a product
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

            // Generate a product variant (size)
            $size = ProductVariant::create([
                "product_id" => $product->id,
                "name" => [
                    "en" => "Size",
                    "nl" => "Maat"
                ],
            ]);

            // Generate product variant options (sizes)
            ProductVariantOption::create([
                "product_variant_id" => $size->id,
                "name" => [
                    "en" => "S",
                    "nl" => "S"
                ],
                "stock" => 1
            ]);
            ProductVariantOption::create([
                "product_variant_id" => $size->id,
                "name" => [
                    "en" => "M",
                    "nl" => "M"
                ],
                "stock" => 2
            ]);
            ProductVariantOption::create([
                "product_variant_id" => $size->id,
                "name" => [
                    "en" => "L",
                    "nl" => "L"
                ],
                "stock" => 3
            ]);
            ProductVariantOption::create([
                "product_variant_id" => $size->id,
                "name" => [
                    "en" => "XL",
                    "nl" => "XL"
                ],
                "stock" => 4
            ]);
            ProductVariantOption::create([
                "product_variant_id" => $size->id,
                "name" => [
                    "en" => "XXL",
                    "nl" => "XXL"
                ],
                "stock" => 5
            ]);
            ProductVariantOption::create([
                "product_variant_id" => $size->id,
                "name" => [
                    "en" => "3XL",
                    "nl" => "3XL"
                ],
                "stock" => 6
            ]);
            
            // Generate some product images
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
