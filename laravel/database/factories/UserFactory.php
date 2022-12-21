<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<User>
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
        $index += 1;
        return [

            'name' => $this->faker->colorName,

            'email' => $this->faker->unique()->safeEmail,

            'password' => bcrypt('123456'),

            'created_at' => $this->faker->dateTime,

            'updated_at' => $this->faker->dateTime,

        ];
    }
}
