<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;

class UserController extends Controller
{
    protected $routePath = 'user';
    protected $viewPath = 'user';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $list = User::query()->paginate();
        return view("$this->viewPath.index", compact('list'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $item = new User();
        $item->_token = csrf_token();
        $item->_uri = "/$this->routePath";
        return view("$this->viewPath.edit", compact('item'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UserRequest $request
     * @return \Illuminate\Routing\Redirector
     */
    public function store(UserRequest $request)
    {
        $item = new User;
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
        $item = User::query()->findOrFail($id);
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
        $item = User::query()->findOrFail($id);
        $item->_token = csrf_token();
        $item->_method = 'PATCH';
        $item->_uri = "/$this->routePath/$item->id";
        return view("$this->viewPath.edit", compact('item'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @param UserRequest $request
     * @return \Illuminate\Routing\Redirector
     */
    public function update($id, UserRequest $request)
    {
        $item = User::query()->findOrFail($id);
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
        User::destroy($id);
        return redirect("/$this->routePath");
    }
}
