<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Contracts\View\View;
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

		$comboAccounts = self::comboAccounts();
		$comboPaid     = self::comboPaid();

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

	public function changeTransactionStatus(Transaction $transaction){
		return $transaction->update(['status' => !$transaction->status]);
	}

	public static function comboAccounts(): array
	{
		$options = ['all' => 'All'];
		Account::all()
			   ->where('user_id', Auth::id())
			   ->each(static function ($row) use (&$options) {
				   $options[$row->id] = $row->name;
			   });

		return $options;
	}

	public static function comboPaid(): array
	{
		return [
			'all' => 'All',
			'0'   => 'To Pay',
			'1'   => 'Paid',
		];
	}

}
