<?php

namespace App\Http\Controllers;

use App\Http\Requests\FromToRequest;
use App\Models\FromTo;

class FromToController extends CrudController
{
	protected string $defaultPath = 'from_to';
	protected string $basePage    = 'Configurations';
	protected $modelClass  = FromTo::class;
	protected $formRequest = FromToRequest::class;
}
