<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentTypeRequest;
use App\Models\PaymentType;

class PaymentTypeController extends CrudController
{
    protected string $defaultPath = 'payment_type';

    protected string $basePage = 'Configurations';

    protected string $modelClass = PaymentType::class;

    protected string $formRequest = PaymentTypeRequest::class;
}
