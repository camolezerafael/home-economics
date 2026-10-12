<?php

namespace Database\Factories;

use App\Models\FromTo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
            'name' => Str::limit($this->faker->company(), 30, ''),
            'type' => $this->faker->randomElement([FromTo::TYPE_FRM, FromTo::TYPE_TO, FromTo::TYPE_FT]),
            'description' => $this->faker->optional()->sentence(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
