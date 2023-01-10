<?php

namespace App\Http\Controllers;

use App\Http\Requests\FromToRequest;
use App\Models\FromTo;

class FromToController extends CrudController
{
	protected $routePath    = 'from_to';
	protected $viewPath     = 'from_to';
	protected $basePage     = 'Configurations';
	protected $homePage     = 'from_tos';
	protected $singularItem = 'From & To';
	protected $pluralItem   = 'From\'s & To\'s';
	protected $modelClass   = FromTo::class;
	protected $formRequest  = FromToRequest::class;
}
