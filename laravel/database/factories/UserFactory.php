<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

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
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('123456'),
            'created_at' => $this->faker->dateTimeInInterval('-6 months', 'today'),
            'updated_at' => $this->faker->dateTime,
        ];
    }
}
