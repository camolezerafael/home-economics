<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
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

		if($request->has('f_date')){
			$f_date = $request->get('f_date');
		}else{
			$f_date = Carbon::now()->format('Y-m');
		}

		$items['RECEI'] = $this->getReceipts($f_date)->OrderBy('date_due', 'DESC')->get();
		$items['FIEX']  = $this->getFexedExpenses($f_date)->OrderBy('date_due', 'DESC')->get();
		$items['VAREX'] = $this->getVariableExpenses($f_date)->OrderBy('date_due', 'DESC')->get();
		$items['PEOP']  = $this->getPeople($f_date)->OrderBy('date_due', 'DESC')->get();
		$items['TAXES'] = $this->getTaxes($f_date)->OrderBy('date_due', 'DESC')->get();
		$items['TRANS'] = $this->getTransfers($f_date)->OrderBy('date_due', 'DESC')->get();

		return view("$this->defaultPath.index", compact('items', 'viewAttributes', 'f_date'));
	}

	public function getReceipts(string $dateFilter): Builder
	{
		return $this->modelClass::where('transaction_type', 'RECEI')
								->where('user_id', Auth::id())
								->whereYear('date_due', Str::of($dateFilter)->substr(0,4)->toString())
								->whereMonth('date_due', Str::of($dateFilter)->substr(5,2)->toString());
	}

	public function getFexedExpenses(string $dateFilter): Builder
	{
		return $this->modelClass::where('transaction_type', 'FIEX')
								->where('user_id', Auth::id())
								->whereYear('date_due', Str::of($dateFilter)->substr(0,4)->toString())
								->whereMonth('date_due', Str::of($dateFilter)->substr(5,2)->toString());
	}

	public function getVariableExpenses(string $dateFilter): Builder
	{
		return $this->modelClass::where('transaction_type', 'VAREX')
								->where('user_id', Auth::id())
								->whereYear('date_due', Str::of($dateFilter)->substr(0,4)->toString())
								->whereMonth('date_due', Str::of($dateFilter)->substr(5,2)->toString());
	}

	public function getPeople(string $dateFilter): Builder
	{
		return $this->modelClass::where('transaction_type', 'PEOP')
								->where('user_id', Auth::id())
								->whereYear('date_due', Str::of($dateFilter)->substr(0,4)->toString())
								->whereMonth('date_due', Str::of($dateFilter)->substr(5,2)->toString());
	}

	public function getTaxes(string $dateFilter): Builder
	{
		return $this->modelClass::where('transaction_type', 'TAXES')
								->where('user_id', Auth::id())
								->whereYear('date_due', Str::of($dateFilter)->substr(0,4)->toString())
								->whereMonth('date_due', Str::of($dateFilter)->substr(5,2)->toString());
	}

	public function getTransfers(string $dateFilter): Builder
	{
		return $this->modelClass::where('transaction_type', 'TRANS')
								->where('user_id', Auth::id())
								->whereYear('date_due', Str::of($dateFilter)->substr(0,4)->toString())
								->whereMonth('date_due', Str::of($dateFilter)->substr(5,2)->toString());
	}
}
