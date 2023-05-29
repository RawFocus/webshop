<?php

namespace Raw\Webshop\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Raw\Webshop\Models\Product;

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
            "listed" => true
        ];
    }
}
