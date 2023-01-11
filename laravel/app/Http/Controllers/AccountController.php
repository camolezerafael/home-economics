<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountRequest;
use App\Models\Account;

class AccountController extends CrudController
{
	protected $defaultPath = 'account';
	protected $basePage    = 'Configurations';
	protected $modelClass  = Account::class;
	protected $formRequest = AccountRequest::class;
}
