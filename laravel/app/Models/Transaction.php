<?php

namespace App\Models;

use App\Models\ModelBase\TransactionBase;
use Illuminate\Database\Eloquent\Builder;

class Transaction extends TransactionBase
{

	public static function getReceipts(): Builder
	{
		return self::where('transaction_type', 'RECEI');
	}

	public static function getFixedExpenses(): Builder
	{
		return self::where('transaction_type', 'FIXEX');
	}

	public static function getVariableExpenses(): Builder
	{
		return self::where('transaction_type', 'VAREX');
	}

	public static function getPeople(): Builder
	{
		return self::where('transaction_type', 'PEOPL');
	}

	public static function getTaxes(): Builder
	{
		return self::where('transaction_type', 'TAXES');
	}

	public static function getTransfers(): Builder
	{
		return self::where('transaction_type', 'TRANS');
	}
}
