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
        return [
            'name' => $this->faker->company(),
            'type' => $this->faker->randomElement([FromTo::TYPE_FRM, FromTo::TYPE_TO, FromTo::TYPE_FT]),
            'description' => $this->faker->optional()->sentence(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
