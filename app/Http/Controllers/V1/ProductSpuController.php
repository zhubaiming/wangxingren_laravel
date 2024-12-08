<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseCollection;
use App\Services\ProductSpuService;
use Illuminate\Http\Request;

class ProductSpuController extends Controller
{
    public function __construct(ProductSpuService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $relations = ['trademark'];

        $payload = $this->service->getList(relations: $relations, paginate: true);

        return (new BaseCollection($payload))->additional(['resource' => 'App\Http\Resources\ProductSpuResource']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $insertData = $request->post();

        $category_ids = explode('-', $insertData['category_id']);
        $spu['category_id'] = $category_ids[count($category_ids) - 1];
        $spu = [
            'title' => $insertData['title'],
            'sub_title' => $insertData['sub_title'],
            'trademark_id' => $insertData['trademark_id'],
            'category_id' => $category_ids[count($category_ids) - 1],
            'saleable' => $insertData['saleable'],
        ];

        $product_spu_id = $this->service->createOne($spu);



        return response()->json([$product_spu_id]);

        /**
         * {
         * "title": null,
         * "sub_title": null,
         * "trademark_id": null,
         * "trademark": 325403,
         * "category_id": null,
         * "category": "1424-1426",
         * "saleable": false,
         * "petBreeds": [
         * 618,
         * 626,
         * 619,
         * 627
         * ]
         * }
         */
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
