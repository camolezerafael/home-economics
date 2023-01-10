<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountTypeRequest;
use App\Models\AccountType;

class AccountTypeController extends CrudController
{
	protected $routePath    = 'account_type';
	protected $viewPath     = 'account_type';
	protected $basePage     = 'Configurations';
	protected $homePage     = 'account_types';
	protected $singularItem = 'Account Type';
	protected $pluralItem   = 'Account Types';
	protected $modelClass   = AccountType::class;
	protected $formRequest  = AccountTypeRequest::class;

}
