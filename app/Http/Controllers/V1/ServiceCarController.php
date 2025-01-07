<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseCollection;
use App\Services\ServiceCarService;
use Illuminate\Http\Request;

class ServiceCarController extends Controller
{
    public function __construct(ServiceCarService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $conditions = [];

//        if (!is_null($request->get('title'))) $conditions[] = ['title', 'like', "%{$request->get('title')}%"];
//        if (!is_null($request->get('categoryId'))) $conditions['category_id'] = $request->get('categoryId');
//        if (!is_null($request->get('trademarkId'))) $conditions['trademark_id'] = $request->get('trademarkId');
//        if (!is_null($request->get('saleable'))) $conditions['saleable'] = $request->get('saleable');

        $payload = $this->service->getList($conditions, paginate: true, page: $request->get('page') ?? $this->page, per_page: $request->get('pageSize') ?? $this->pageSize);

        return (new BaseCollection($payload))->additional(['resource' => 'App\Http\Resources\ServiceCarResource', 'format' => __FUNCTION__]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
