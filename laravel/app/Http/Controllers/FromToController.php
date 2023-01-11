<?php

namespace App\Http\Controllers;

use App\Http\Requests\FromToRequest;
use App\Models\FromTo;

class FromToController extends CrudController
{
	protected $defaultPath = 'from_to';
	protected $basePage     = 'Configurations';
	protected $modelClass   = FromTo::class;
	protected $formRequest  = FromToRequest::class;
}
