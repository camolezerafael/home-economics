<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountTypeRequest;
use App\Models\AccountType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class CrudController extends Controller
{
	protected $routePath    = '';
	protected $viewPath     = '';
	protected $basePage     = '';
	protected $homePage     = '';
	protected $singularItem = '';
	protected $pluralItem   = '';
	protected $modelClass   = Model::class;
	protected $formRequest  = FormRequest::class;

	protected function viewAttributes()
	{
		return $this->viewAttributes = [
			'routePath'    => $this->routePath,
			'viewPath'     => $this->viewPath,
			'homePage'     => $this->homePage,
			'basePage'     => __($this->basePage),
			'singularItem' => __($this->singularItem),
			'pluralItem'   => __($this->pluralItem),
		];
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Contracts\View\View
	 */
	public function index()
	{
		$viewAttributes = $this->viewAttributes();
		$items          = $this->modelClass::query()->paginate(15);
		return view("$this->viewPath.index", compact('items', 'viewAttributes'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Contracts\View\View
	 */
	public function create()
	{
		$viewAttributes = $this->viewAttributes();
		$item           = new $this->modelClass();
		$item->_token   = csrf_token();
		$item->_uri     = "/$this->routePath";
		return view("$this->viewPath.edit", compact('item', 'viewAttributes'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return \Illuminate\Routing\Redirector
	 */
	public function store(Request $request)
	{
		$item = new $this->modelClass;
		$request->validate((new $this->formRequest)->rules());
		$item->fill($request->validated());
		$item->save();
		return redirect("/$this->routePath/$item->id/edit");
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id
	 * @return \Illuminate\Contracts\View\View
	 */
	public function edit($id)
	{
		$viewAttributes = $this->viewAttributes();
		$item           = $this->modelClass::query()->findOrFail($id);
		$item->_token   = csrf_token();
		$item->_method  = 'PATCH';
		$item->_uri     = "/$this->routePath/$item->id";
		return view("$this->viewPath.edit", compact('item', 'viewAttributes'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param int $id
	 * @param Request $request
	 * @return \Illuminate\Routing\Redirector
	 */
	public function update($id, Request $request)
	{
		$item = $this->modelClass::query()->findOrFail($id);
		$request->validate((new $this->formRequest)->rules());
		$item->update($request->validated());
		return redirect("/$this->routePath/$item->id/edit");
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id
	 * @return \Illuminate\Routing\Redirector
	 */
	public function destroy($id)
	{
		$this->modelClass::destroy($id);
		return redirect("/$this->routePath");
	}

}
