<?php

namespace Database\Factories;

use App\Models\FromTo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FromTo>
 */
class FromToFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FromTo::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
		static $index = 0;
		$index++;
		return [
			'name' => $this->faker->colorName(),
			'type' => $this->faker->randomElement(['A', 'B', 'C', 'D']),
		];
    }
}
