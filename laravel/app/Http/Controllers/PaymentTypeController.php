<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentTypeRequest;
use App\Models\PaymentType;

class PaymentTypeController extends CrudController
{
	protected $routePath    = 'payment_type';
	protected $viewPath     = 'payment_type';
	protected $basePage     = 'Configurations';
	protected $homePage     = 'payment_types';
	protected $singularItem = 'Payment Type';
	protected $pluralItem   = 'Payment Types';
	protected $modelClass   = PaymentType::class;
	protected $formRequest  = PaymentTypeRequest::class;
}
