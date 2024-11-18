<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\SysGoodsCategory;
use App\Services\GoodsCategoryService;
use Illuminate\Http\Request;

class GoodCategoryController extends Controller
{
    public function __construct(GoodsCategoryService $service)
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
        $this->service->createOne();
        $brand_id = $request->input('brand_id');
        $data = $request->input();
        unset($data['brand_id']);
        $model = SysGoodsCategory::create($data);
        $model->brands()->attach($brand_id);
        $model->refresh();
        return response()->json($model);
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
