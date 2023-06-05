<?php

namespace RawFocus\Webshop\database\factories;

use RawFocus\Webshop\Models\Order;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "user_id" => 1,
            "name" => $this->faker->name,
            "email" => $this->faker->email,
            "address_street" => $this->faker->streetAddress,
            "address_country" => "NL",
            "address_postal_code" => $this->faker->postcode,
            "address_city" => $this->faker->city,
            "total_price" => $this->faker->randomFloat(2, 0, 100),
            "order_status" => "open",
            "payment_status" => "pending",
            "payment_method" => "ideal",
            "payment_id" => null
        ];
    }
}
