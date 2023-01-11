<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountTypeRequest;
use App\Models\AccountType;

class AccountTypeController extends CrudController
{
	protected $defaultPath = 'account_type';
	protected $basePage    = 'Configurations';
	protected $modelClass  = AccountType::class;
	protected $formRequest = AccountTypeRequest::class;

}
