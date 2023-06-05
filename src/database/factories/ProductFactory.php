<?php

namespace RawFocus\Webshop\database\factories;

use RawFocus\Webshop\Models\Product;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
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
            "listed" => true,
            "type" => "tshirt",
        ];
    }
}
