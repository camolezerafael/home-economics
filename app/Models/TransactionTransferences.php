<?php

	namespace App\Models;

	class TransactionTransferences extends Transaction
	{
		protected $fillable = [
			'user_id',
			'description',
			'from_to_id',
			'category_id',
			'payment_type_id',
			'amount',
			'date_due',
		];

		public static function keys(): array
		{
			return [
				'id',
				'account_id',
				'transaction_type',
				'description',
				'amount' => 'decimal:2',
				'status',
				'date_due',
				'date_payment',
			];
		}
	}
