<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountTypeRequest;
use App\Models\AccountType;

class AccountTypeController extends CrudController
{
	protected string $defaultPath = 'account_type';
	protected string $basePage    = 'Configurations';
	protected $modelClass  = AccountType::class;
	protected $formRequest = AccountTypeRequest::class;

}
