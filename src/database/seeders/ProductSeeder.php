<?php

namespace Raw\Webshop\database\seeders;

use DB;
use Raw\Webshop\Models\ProductImage;

use Illuminate\Database\Seeder;
use Raw\Webshop\Models\Product;

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

        $product = Product::create([
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

        ProductImage::create([
            "product_id" => $product->id,
            "path" => "/storage/images/webshop/products/default.jpg"
        ]);

    }
}
