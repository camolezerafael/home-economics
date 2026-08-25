<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;

class CategoryController extends CrudController
{
    protected string $defaultPath = 'category';

    protected string $basePage = 'Configurations';

    protected string $modelClass = Category::class;

    protected string $formRequest = CategoryRequest::class;
}
