<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

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
			'transaction_type' => $this->faker->text(5),
			'description' => $this->faker->text(30),
			'value' => $this->faker->randomFloat(0,1,999999),
			'status' => $this->faker->boolean(),
			'date_due' => $this->faker->dateTimeInInterval('-1 year', '+1 year'),
			'date_payment' => $this->faker->randomElement([$this->faker->dateTimeInInterval('-1 year', '+6 months'), null]),
		];
    }
}
