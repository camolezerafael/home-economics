<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountRequest;
use App\Models\Account;
use App\Models\AccountType;

class AccountController extends CrudController
{
    protected string $defaultPath = 'account';

    protected string $basePage = 'Configurations';

    protected string $modelClass = Account::class;

    protected string $formRequest = AccountRequest::class;

    protected function beforeSave(array &$columns): void
    {
        if (array_key_exists('decimal_precision', $columns)) {
            $precision = $columns['decimal_precision'];
            unset($columns['decimal_precision']);
            $columns = ['decimal_precision' => $precision] + $columns;
        }

        parent::beforeSave($columns);
    }

    protected function getAggregates(): array
    {
        return [
            'account_type' => $this->getDefaultOptionsModel(AccountType::class),
        ];
    }
}
