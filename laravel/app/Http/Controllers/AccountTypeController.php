<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateAccountTypeRequest;
use App\Models\AccountType;

class AccountTypeController extends Controller
{
    protected $routePath = 'account_type';
    protected $viewPath = 'account_type';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $items = AccountType::query()->paginate(5);
        return view("$this->viewPath.index", compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $item = new AccountType();
        $item->_token = csrf_token();
        $item->_uri = "/$this->routePath";
        return view("$this->viewPath.edit", compact('item'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateAccountTypeRequest $request
     * @return \Illuminate\Routing\Redirector
     */
    public function store(CreateAccountTypeRequest $request)
    {
        $item = new AccountType;
        $item->fill($request->validated());
        $item->save();
        return redirect("/$this->routePath/$item->id");
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $item = AccountType::query()->findOrFail($id);
        return view("$this->viewPath.show", compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $item = AccountType::query()->findOrFail($id);
        $item->_token = csrf_token();
        $item->_method = 'PATCH';
        $item->_uri = "/$this->routePath/$item->id";
        return view("$this->viewPath.edit", compact('item'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @param CreateAccountTypeRequest $request
     * @return \Illuminate\Routing\Redirector
     */
    public function update($id, CreateAccountTypeRequest $request)
    {
        $item = AccountType::query()->findOrFail($id);
        $item->update($request->validated());
        return redirect("/$this->routePath/$item->id");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        AccountType::destroy($id);
        return redirect("/$this->routePath");
    }
}
