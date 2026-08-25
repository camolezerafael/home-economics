<?php

namespace Database\Factories;

use App\Models\PaymentType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PaymentType>
 */
class PaymentTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PaymentType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->randomElement(['PIX', 'Cartão Crédito', 'Cartão Débito', 'Dinheiro', 'Boleto']),
            'description' => $this->faker->optional()->sentence(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
