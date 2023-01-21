<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Account;
use App\Models\Category;
use App\Models\FromTo;
use App\Models\PaymentType;
use App\Models\Transaction;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TransactionController extends CrudController
{
	protected string $defaultPath = 'transaction';
	protected string $basePage    = 'Transactions';
	protected $modelClass  = Transaction::class;
	protected $formRequest = TransactionRequest::class;

	protected function viewAttributes():array
	{
		return [
			'routePath'    => $this->defaultPath,
			'basePage'     => __($this->basePage),
			'viewPath'     => $this->defaultPath,
			'homePage'     => Str::of($this->defaultPath)->headline()->pluralStudly()->snake()->toString(),
			'singularItem' => __('Movement'),
			'pluralItem'   => __('Movements'),
		];
	}

	public function index(Request $request): View
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

		$comboAccounts = self::comboAccounts(true);
		$comboPaid     = self::comboPaid(true);

		$model = new $this->modelClass();

		$monthTotals = $model->getMonthTotals($f_date);

		$items = $model->getValues($f_date, $f_acc, $f_pay);

		$monthBalance     = $model->getFullBalance($f_date, $f_acc, 'all');
		$finalBalance     = $model->getFullBalance($f_date, $f_acc, 1, true);
		$estimatedBalance = $model->getFullBalance($f_date, $f_acc, 'all', true);

		return view("$this->defaultPath.index", compact(
			'viewAttributes',
			'comboAccounts',
			'comboPaid',
			'f_date',
			'f_acc',
			'f_pay',
			'items',
			'monthTotals',
			'monthBalance',
			'finalBalance',
			'estimatedBalance'
		));
	}

	public function edit($id): View
	{
		$comboAccounts = self::comboAccounts();
		$comboPaid     = self::comboPaid();
		$comboTypes = self::comboTypes();
		$comboFromTos = self::comboFromTos();
		$comboCategories = self::comboCategories();
		$comboPaymentTypes = self::comboPaymentTypes();

		$viewAttributes = $this->viewAttributes();
		$item           = $this->modelClass::query()->findOrFail($id);
		$item->_token   = csrf_token();
		$item->_method  = 'PATCH';
		$item->_uri     = "/$this->defaultPath/$item->id";
		return view(
			"$this->defaultPath.edit",
			compact(
				'item',
				'viewAttributes',
				'comboAccounts',
				'comboPaid',
				'comboTypes',
				'comboFromTos',
				'comboCategories',
				'comboPaymentTypes'
			)
		);
	}

	public function changeTransactionStatus(Transaction $transaction): bool
	{
		return $transaction->update(['status' => !$transaction->status]);
	}

	public static function comboAccounts($withAll = false): array
	{
		$options = $withAll ? ['all' => 'All'] : [];
		Account::all()
			   ->where('user_id', Auth::id())
			   ->each(static function ($row) use (&$options) {
				   $options[$row->id] = $row->name;
			   });

		return $options;
	}

	public static function comboPaid($withAll = false): array
	{
		$options = $withAll ? ['all' => 'All'] : [];
		return $options + [
				'0' => 'To Pay',
				'1' => 'Paid',
			];
	}

	public static function comboTypes($withAll = false): array
	{
		$options = $withAll ? ['all' => 'All'] : [];
		return $options + [
				'RECEI' => __('Receipts'),
				'FIXEX' => __('Fixed Expenses'),
				'VAREX' => __('Variable Expenses'),
				'PEOPL' => __('People'),
				'TAXES' => __('Taxes'),
				'TRANS' => __('Transferences'),
			];
	}

	public static function comboFromTos($withAll = false): array
	{
		$options = $withAll ? ['all' => 'All'] : [];
		FromTo::all()
			  ->where('user_id', Auth::id())
			  ->each(static function ($row) use (&$options) {
				  $options[$row->id] = $row->name;
			  });

		return $options;
	}

	public static function comboCategories($withAll = false): array
	{
		$options = $withAll ? ['all' => 'All'] : [];
		Category::all()
				->where('user_id', Auth::id())
				->each(static function ($row) use (&$options) {
					$options[$row->id] = $row->name;
				});

		return $options;
	}

	public static function comboPaymentTypes($withAll = false): array
	{
		$options = $withAll ? ['all' => 'All'] : [];
		PaymentType::all()
				   ->where('user_id', Auth::id())
				   ->each(static function ($row) use (&$options) {
					   $options[$row->id] = $row->name;
				   });

		return $options;
	}

}
