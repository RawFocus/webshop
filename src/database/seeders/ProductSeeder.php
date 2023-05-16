<?php

namespace Database\Seeders;

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
            "title" => "title",
            "summary" => "summary",
            "price" => 26,
            "stock" => 10,
            "listed" => true
        ]);

    }
}
