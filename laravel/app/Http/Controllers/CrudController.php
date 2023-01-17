<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class CrudController extends Controller
{
	protected $defaultPath = '';
	protected $basePage    = '';
	protected $modelClass  = Model::class;
	protected $formRequest = FormRequest::class;

	protected function viewAttributes()
	{
		return $this->viewAttributes = [
			'routePath'    => $this->defaultPath,
			'basePage'     => __($this->basePage),
			'viewPath'     => $this->defaultPath,
			'homePage'     => Str::of($this->defaultPath)->headline()->pluralStudly()->snake()->toString(),
			'singularItem' => Str::of(__($this->defaultPath))->headline()->singular()->toString(),
			'pluralItem'   => Str::of(__($this->defaultPath))->headline()->pluralStudly()->toString(),
		];
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Contracts\View\View
	 */
	public function index(Request $request)
	{
		$viewAttributes = $this->viewAttributes();
		$items          = $this->modelClass::query()->paginate(15);

		if (View::exists("$this->defaultPath.index")) {
			return view("$this->defaultPath.index", compact('items', 'viewAttributes'));
		}
		return view('pages.default-list', compact('items', 'viewAttributes'));
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
		$item->_uri     = "/$this->defaultPath";
		return view("$this->defaultPath.edit", compact('item', 'viewAttributes'));
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
		return redirect("/$this->defaultPath/$item->id/edit");
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
		$item->_uri     = "/$this->defaultPath/$item->id";
		return view("$this->defaultPath.edit", compact('item', 'viewAttributes'));
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
		return redirect("/$this->defaultPath/$item->id/edit");
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
		return redirect("/$this->defaultPath");
	}

}
