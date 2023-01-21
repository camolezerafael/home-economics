<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;

class CategoryController extends CrudController
{
	protected string $defaultPath = 'category';
	protected string $basePage    = 'Configurations';
	protected $modelClass  = Category::class;
	protected $formRequest = CategoryRequest::class;
}
