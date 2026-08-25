<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountTypeRequest;
use App\Models\AccountType;

class AccountTypeController extends CrudController
{
    protected string $defaultPath = 'account_type';

    protected string $basePage = 'Configurations';

    protected string $modelClass = AccountType::class;

    protected string $formRequest = AccountTypeRequest::class;

    protected function getAggregates(): array
    {
        return [];
    }
}
