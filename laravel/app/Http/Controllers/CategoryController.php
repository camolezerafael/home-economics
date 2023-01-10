<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;

class CategoryController extends CrudController
{
	protected $routePath    = 'category';
	protected $viewPath     = 'category';
	protected $basePage     = 'Configurations';
	protected $homePage     = 'categories';
	protected $singularItem = 'Category';
	protected $pluralItem   = 'Categories';
	protected $modelClass   = Category::class;
	protected $formRequest  = CategoryRequest::class;
}
