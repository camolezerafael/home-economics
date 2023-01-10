<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Transaction;

class TransactionController extends CrudController
{
	protected $routePath    = 'transaction';
	protected $viewPath     = 'transaction';
	protected $basePage     = 'Transactions';
	protected $homePage     = 'transaction';
	protected $singularItem = 'Movement';
	protected $pluralItem   = 'Movements';
	protected $modelClass   = Transaction::class;
	protected $formRequest  = TransactionRequest::class;
}
