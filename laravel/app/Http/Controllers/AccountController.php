<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountRequest;
use App\Models\Account;

class AccountController extends CrudController
{
	protected $routePath    = 'account';
	protected $viewPath     = 'account';
	protected $basePage     = 'Configurations';
	protected $homePage     = 'accounts';
	protected $singularItem = 'Account';
	protected $pluralItem   = 'Accounts';
	protected $modelClass   = Account::class;
	protected $formRequest  = AccountRequest::class;
}
