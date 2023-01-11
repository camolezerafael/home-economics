<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Transaction;

class TransactionController extends CrudController
{
	protected $defaultPath = 'transaction';
	protected $basePage     = 'Transactions';
	protected $viewPath    = 'transaction';
	protected $homePage     = 'transaction';
	protected $singularItem = 'Movement';
	protected $pluralItem   = 'Movements';
	protected $modelClass   = Transaction::class;
	protected $formRequest  = TransactionRequest::class;
}
