<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TransactionController extends CrudController
{
	protected $defaultPath = 'transaction';
	protected $basePage    = 'Transactions';
	protected $modelClass  = Transaction::class;
	protected $formRequest = TransactionRequest::class;

	protected function viewAttributes()
	{
		return $this->viewAttributes = [
			'routePath'    => $this->defaultPath,
			'basePage'     => __($this->basePage),
			'viewPath'     => $this->defaultPath,
			'homePage'     => Str::of($this->defaultPath)->headline()->pluralStudly()->snake()->toString(),
			'singularItem' => __('Movement'),
			'pluralItem'   => __('Movements'),
		];
	}

	public function index(Request $request)
	{
		$viewAttributes = $this->viewAttributes();

		if ($request->has('f_date')) {
			$f_date = $request->get('f_date');
		} else {
			$f_date = Carbon::now()->format('Y-m');
		}

		if ($request->has('f_acc')) {
			$f_acc = $request->get('f_acc');
		} else {
			$f_acc = 'all';
		}

		if ($request->has('f_pay')) {
			$f_pay = $request->get('f_pay');
		} else {
			$f_pay = 'all';
		}

		$comboAccounts = self::comboAccounts();
		$comboPaid     = self::comboPaid();

		$monthTotals = $this->getMonthTotals($f_date);

		$items = $this->getValues($f_date, $f_acc, $f_pay);

		return view("$this->defaultPath.index", compact(
			'viewAttributes',
			'comboAccounts',
			'comboPaid',
			'f_date',
			'f_acc',
			'f_pay',
			'items',
			'monthTotals',
		));
	}

	public static function comboAccounts()
	{
		$options = ['all' => 'All'];
		Account::all()->each(static function ($row) use (&$options) {
			$options[$row->id] = $row->name;
		});

		return $options;
	}

	public static function comboPaid()
	{
		return [
			'all' => 'All',
			'0'   => 'To Pay',
			'1'   => 'Paid',
		];
	}

	public function getValues($f_date, $f_acc, $f_pay): array
	{
		$items['RECEI'] = $this->applyFilters($this->modelClass::getReceipts(), $f_date, $f_acc, $f_pay)->get();
		$items['FIXEX'] = $this->applyFilters($this->modelClass::getFixedExpenses(), $f_date, $f_acc, $f_pay)->get();
		$items['VAREX'] = $this->applyFilters($this->modelClass::getVariableExpenses(), $f_date, $f_acc, $f_pay)->get();
		$items['PEOPL'] = $this->applyFilters($this->modelClass::getPeople(), $f_date, $f_acc, $f_pay)->get();
		$items['TAXES'] = $this->applyFilters($this->modelClass::getTaxes(), $f_date, $f_acc, $f_pay)->get();
		$items['TRANS'] = $this->applyFilters($this->modelClass::getTransfers(), $f_date, $f_acc, $f_pay)->get();

		return $items;
	}

	public function getMonthTotals($f_date): array
	{
		$totals['RECEI']['PAID'] = $this->applyFilters($this->modelClass::getReceipts(), $f_date, 'all', 1)->sum('value');
		$totals['RECEI']['TO_PAY'] = $this->applyFilters($this->modelClass::getReceipts(), $f_date, 'all', 0)->sum('value');

		$totals['EXPEN']['PAID'] = $this->applyFilters($this->modelClass::getFixedExpenses(), $f_date, 'all', 1)->sum('value');
		$totals['EXPEN']['PAID'] += $this->applyFilters($this->modelClass::getVariableExpenses(), $f_date, 'all', 1)->sum('value');
		$totals['EXPEN']['PAID'] += $this->applyFilters($this->modelClass::getPeople(), $f_date, 'all', 1)->sum('value');
		$totals['EXPEN']['PAID'] += $this->applyFilters($this->modelClass::getTaxes(), $f_date, 'all', 1)->sum('value');

		$totals['EXPEN']['TO_PAY'] = $this->applyFilters($this->modelClass::getFixedExpenses(), $f_date, 'all', 0)->sum('value');
		$totals['EXPEN']['TO_PAY'] += $this->applyFilters($this->modelClass::getVariableExpenses(), $f_date, 'all', 0)->sum('value');
		$totals['EXPEN']['TO_PAY'] += $this->applyFilters($this->modelClass::getPeople(), $f_date, 'all', 0)->sum('value');
		$totals['EXPEN']['TO_PAY'] += $this->applyFilters($this->modelClass::getTaxes(), $f_date, 'all', 0)->sum('value');

		return $totals;
	}

	public function applyFilters(Builder $query, string $dateFilter, string $accountFilter, string $paidFilter): Builder
	{
		return $query->where('user_id', Auth::id())
					 ->whereYear('date_due', Str::of($dateFilter)->substr(0, 4)->toString())
					 ->whereMonth('date_due', Str::of($dateFilter)->substr(5, 2)->toString())
					 ->when($accountFilter !== 'all', static function ($filter) use ($accountFilter) {
						 $filter->where('account_id', $accountFilter);
					 })
					 ->when($paidFilter !== 'all', static function ($filter) use ($paidFilter) {
						 $filter->where('status', $paidFilter);
					 })
					 ->OrderBy('date_due');
	}
}
