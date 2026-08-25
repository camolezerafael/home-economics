<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Account::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->randomElement(['Conta Corrente', 'Poupança', 'Carteira', 'Investimentos']),
            'description' => $this->faker->optional()->sentence(3),
            'initial_balance' => 0,
            'decimal_precision' => 2,
            'user_id' => \App\Models\User::factory(),
            'type_id' => \App\Models\AccountType::factory(),
        ];
    }
}
