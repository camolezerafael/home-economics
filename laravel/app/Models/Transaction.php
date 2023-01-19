<?php

namespace App\Models;

use App\Models\ModelBase\TransactionBase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Transaction extends TransactionBase
{
	public function getReceipts(): Builder
	{
		return self::where('transaction_type', 'RECEI');
	}

	public function getFixedExpenses(): Builder
	{
		return self::where('transaction_type', 'FIXEX');
	}

	public function getVariableExpenses(): Builder
	{
		return self::where('transaction_type', 'VAREX');
	}

	public function getPeople(): Builder
	{
		return self::where('transaction_type', 'PEOPL');
	}

	public function getTaxes(): Builder
	{
		return self::where('transaction_type', 'TAXES');
	}

	public function getTransfers(): Builder
	{
		return self::where('transaction_type', 'TRANS');
	}

	public function getValues($dateFilter, $accountFilter, $paidFilter): array
	{
		$items['RECEI'] = $this->applyFilters($this->getReceipts(), $dateFilter, $accountFilter, $paidFilter)->get();
		$items['FIXEX'] = $this->applyFilters($this->getFixedExpenses(), $dateFilter, $accountFilter, $paidFilter)->get();
		$items['VAREX'] = $this->applyFilters($this->getVariableExpenses(), $dateFilter, $accountFilter, $paidFilter)->get();
		$items['PEOPL'] = $this->applyFilters($this->getPeople(), $dateFilter, $accountFilter, $paidFilter)->get();
		$items['TAXES'] = $this->applyFilters($this->getTaxes(), $dateFilter, $accountFilter, $paidFilter)->get();
		$items['TRANS'] = $this->applyFilters($this->getTransfers(), $dateFilter, $accountFilter, $paidFilter)->get();

		return $items;
	}

	public function getMonthTotals($dateFilter): array
	{
		$totals = [
			'RECEI' => [
				'PAID'   => 0,
				'TO_PAY' => 0,
				'PERC'   => 0,
			],
			'EXPEN' => [
				'PAID'   => 0,
				'TO_PAY' => 0,
				'PERC'   => 0,
			]
		];

		$this->applyFilters($this->getReceipts(), $dateFilter, 'all', 1)->each(static function ($row) use (&$totals) {
			$totals['RECEI']['PAID'] += $row->value;
		});

		$this->applyFilters($this->getReceipts(), $dateFilter, 'all', 'all')->each(static function ($row) use (&$totals) {
			$totals['RECEI']['TO_PAY'] += $row->value;
		});


		$this->applyFilters($this->getFixedExpenses(), $dateFilter, 'all', 1)->each(static function ($row) use (&$totals) {
			$totals['EXPEN']['PAID'] += $row->value;
		});

		$this->applyFilters($this->getVariableExpenses(), $dateFilter, 'all', 1)->each(static function ($row) use (&$totals) {
			$totals['EXPEN']['PAID'] += $row->value;
		});

		$this->applyFilters($this->getPeople(), $dateFilter, 'all', 1)->each(static function ($row) use (&$totals) {
			$totals['EXPEN']['PAID'] += $row->value;
		});

		$this->applyFilters($this->getTaxes(), $dateFilter, 'all', 1)->each(static function ($row) use (&$totals) {
			$totals['EXPEN']['PAID'] += $row->value;
		});


		$this->applyFilters($this->getFixedExpenses(), $dateFilter, 'all', 'all')->each(static function ($row) use (&$totals) {
			$totals['EXPEN']['TO_PAY'] += $row->value;
		});
		$this->applyFilters($this->getVariableExpenses(), $dateFilter, 'all', 'all')->each(static function ($row) use (&$totals) {
			$totals['EXPEN']['TO_PAY'] += $row->value;
		});
		$this->applyFilters($this->getPeople(), $dateFilter, 'all', 'all')->each(static function ($row) use (&$totals) {
			$totals['EXPEN']['TO_PAY'] += $row->value;
		});
		$this->applyFilters($this->getTaxes(), $dateFilter, 'all', 'all')->each(static function ($row) use (&$totals) {
			$totals['EXPEN']['TO_PAY'] += $row->value;
		});

		$totals['RECEI']['PERC'] = number_format((($totals['RECEI']['PAID'] === 0) ? 0 : ($totals['RECEI']['PAID'] / $totals['RECEI']['TO_PAY'] * 100)), 0);
		$totals['EXPEN']['PERC'] = number_format((($totals['EXPEN']['PAID'] === 0) ? 0 : ($totals['EXPEN']['PAID'] / $totals['EXPEN']['TO_PAY'] * 100)), 0);

		return $totals;
	}

	public function applyFilters(Builder $query, string $dateFilter, string $accountFilter, string $paidFilter): Builder
	{
		$date = \Carbon\Carbon::createFromFormat('Y-m-d', $dateFilter . '-01');

		return $query->where('user_id', Auth::id())
					 ->whereBetween('date_due', [$dateFilter . '-01', $date->lastOfMonth()->format('Y-m-d')])
					 ->when($accountFilter !== 'all', static function ($filter) use ($accountFilter) {
						 $filter->where('account_id', $accountFilter);
					 })
					 ->when($paidFilter !== 'all', static function ($filter) use ($paidFilter) {
						 $filter->where('status', $paidFilter);
					 })
					 ->OrderBy('date_due');
	}

	public function getFullBalance(string $dateFilter, string $accountFilter, string $paidFilter, $backwardsBalance = false)
	{
		$where         = 'WHERE user_id = ' . Auth::id();
		$whereDate     = '';
		$whereAccount  = '';
		$whereAccount2 = '';
		$wherePaid     = '';

		$date = \Carbon\Carbon::createFromFormat('Y-m-d', $dateFilter . '-01');

		if ($dateFilter !== '') {
			$whereDate = ' AND date_due BETWEEN \'' . $dateFilter . '-01\' AND \'' . $date->lastOfMonth()->format('Y-m-d') . '\'';

			if($backwardsBalance){
				$whereDate = ' AND date_due <= \'' . $date->lastOfMonth()->format('Y-m-d') . '\'';
			}
		}

		if ($accountFilter !== 'all') {
			$whereAccount  = ' AND account_id = ' . $accountFilter;
			$whereAccount2 = ' AND id = ' . $accountFilter;
		}

		if ($paidFilter !== 'all') {
			$wherePaid = ' AND status = ' . $paidFilter;
		}

		$where2 = $where . $whereAccount2;

		$where .= $whereDate . $whereAccount . $wherePaid;

		$sql = "
			SELECT
				SUM(receipts) - SUM(expenses) as month_balance,
				SUM(receipts) + SUM(balance) - SUM(expenses) as final_balance

			FROM (
					SELECT
						SUM(value) / POWER(10,2) AS receipts,
						0 AS expenses,
						0 AS balance
					FROM
						balance_receipts
					$where

					UNION

					SELECT
						0 AS receipts,
						SUM(value) / POWER(10,2) AS expenses,
						0 AS balance
					FROM
						balance_expenses
					$where

					UNION

					SELECT
						0 AS expenses,
						0 AS receipts,
						SUM(initial_balance) / POWER(10,2) AS balance
					FROM
						accounts
					$where2
				)
				AS view_balance";
		// if($backwardsBalance && $paidFilter == 'all'){
		// 	dd($sql);
		// }
		return DB::selectOne(DB::raw($sql));
	}

}
