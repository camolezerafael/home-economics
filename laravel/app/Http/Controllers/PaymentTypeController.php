<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentTypeRequest;
use App\Models\PaymentType;

class PaymentTypeController extends CrudController
{
	protected $defaultPath = 'payment_type';
	protected $basePage     = 'Configurations';
	protected $modelClass   = PaymentType::class;
	protected $formRequest  = PaymentTypeRequest::class;
}
