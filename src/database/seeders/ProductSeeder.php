<?php

namespace Raw\Webshop\database\seeders;

use DB;

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

        Product::create([
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

    }
}
