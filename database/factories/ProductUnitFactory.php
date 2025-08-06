<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\ProductUnit;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductUnit>
 */
class ProductUnitFactory extends Factory
{
    protected $model = ProductUnit::class;

    public function definition(): array
    {
        return [
            'store_name' => $this->faker->company,
            'coding' => Str::random(10), // Generate random 10-character code
            'unit_type' => $this->faker->randomElement(['barcode', 'not_barcode']),
            'user_id_maker' => User::factory(),
            'description' => $this->faker->sentence,
            'can_have_float_value' => $this->faker->boolean,
        ];
    }
}
