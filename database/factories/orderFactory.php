<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\order>
 */
class orderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => $this->faker->randomDigitNot(0),
            'order_date' => $this->faker->dateTimeBetween('-5 year', 'now'),
            'subtotal' => $this->faker->randomFloat(2, 100, 1000),
            'taxes' => $this->faker->randomFloat(2, 10, 100),
            'total' => $this->faker->randomFloat(2, 100, 1000),
        ];
    }
}
