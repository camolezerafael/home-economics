<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountRequest;
use App\Models\Account;
use App\Models\AccountType;

class AccountController extends CrudController
{
	protected string $defaultPath = 'account';
	protected string $basePage    = 'Configurations';
	protected $modelClass  = Account::class;
	protected $formRequest = AccountRequest::class;

	protected function getAggregates(): array
	{
		return [
			'account_type' => $this->getDefaultOptionsModel( AccountType::class)
		];
	}

}
