<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Account;
use App\Models\AccountType;
use App\Models\Category;
use App\Models\FromTo;
use App\Models\PaymentType;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Seed the application's database.
	 *
	 * @return void
	 */
	public function run(): void
	{
		User::factory(10)->create();
		AccountType::factory(10)->create();
		Category::factory(10)->create();
		FromTo::factory(10)->create();
		PaymentType::factory(10)->create();

		Account::factory(5)
			   ->state(new Sequence(
				   static fn($sequence) => [
					   'user_id' => User::all()->random(),
					   'type_id' => AccountType::all()->random(),
				   ]))
			   ->create();

		Transaction::factory(50)
				   ->state(new Sequence(
					   static fn($sequence) => [
						   'user_id'         => User::all()->random(),
						   'account_id'      => Account::all()->random(),
						   'from_to_id'         => FromTo::all()->random(),
						   'category_id'     => Category::all()->random(),
						   'payment_type_id' => PaymentType::all()->random(),
					   ]
				   ))
				   ->create();
	}
}
