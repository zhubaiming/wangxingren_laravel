<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\GoodsSpuService;
use Illuminate\Http\Request;

class GoodsController extends Controller
{
    public function __construct(GoodsSpuService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // todo: 需要对入参进行前置校验

        if ($this->service->createOne(...$request->only(['spu', 'detail', 'service_times', 'spec_groups', 'skus']))) {
            return $this->setStatusCode(201)->created();
        } else {
            return $this->internalError();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
