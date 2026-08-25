<?php

namespace Database\Factories;

use App\Models\AccountType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AccountType>
 */
class AccountTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AccountType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->randomElement(['Conta Corrente', 'Poupança', 'Cartão de Crédito', 'Carteira Física', 'Carteira Digital']),
            'description' => $this->faker->optional()->sentence(3),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
