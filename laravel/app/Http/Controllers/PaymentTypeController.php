<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePaymentTypeRequest;
use App\Models\PaymentType;

class PaymentTypeController extends Controller
{
    protected $routePath = 'payment_type';
    protected $viewPath = 'payment_type';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $page = PaymentType::query()->paginate();
        return view("$this->viewPath.index", compact('page'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $item = new PaymentType();
        $item->_token = csrf_token();
        $item->_uri = "/$this->routePath";
        return view("$this->viewPath.edit", compact('item'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreatePaymentTypeRequest $request
     * @return \Illuminate\Routing\Redirector
     */
    public function store(CreatePaymentTypeRequest $request)
    {
        $item = new PaymentType;
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
        $item = PaymentType::query()->findOrFail($id);
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
        $item = PaymentType::query()->findOrFail($id);
        $item->_token = csrf_token();
        $item->_method = 'PATCH';
        $item->_uri = "/$this->routePath/$item->id";
        return view("$this->viewPath.edit", compact('item'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @param CreatePaymentTypeRequest $request
     * @return \Illuminate\Routing\Redirector
     */
    public function update($id, CreatePaymentTypeRequest $request)
    {
        $item = PaymentType::query()->findOrFail($id);
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
        PaymentType::destroy($id);
        return redirect("/$this->routePath");
    }
}
