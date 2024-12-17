<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\ProductSpuResource;
use App\Models\ProductSpecGroup;
use App\Models\ProductSpu;
use App\Models\ProductSpuDetail;
use App\Services\ProductSpuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductSpuController extends Controller
{
    public function __construct(ProductSpuService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $conditions = [];

        if (!is_null($request->get('title'))) $conditions[] = ['title', 'like', "%{$request->get('title')}%"];
        if (!is_null($request->get('categoryId'))) $conditions['category_id'] = $request->get('categoryId');
        if (!is_null($request->get('trademarkId'))) $conditions['trademark_id'] = $request->get('trademarkId');
        if (!is_null($request->get('saleable'))) $conditions['saleable'] = $request->get('saleable');

        $relations = ['category', 'trademark'];

        $payload = $this->service->getList($conditions, relations: $relations, paginate: true, page: $request->get('page') ?? $this->page, per_page: $request->get('pageSize') ?? $this->pageSize);

        return (new BaseCollection($payload))->additional(['resource' => 'App\Http\Resources\ProductSpuResource', 'format' => __FUNCTION__]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $insertData = arrHumpToLine($request->post());

        $spuData = [
            'title' => $insertData['title'],
            'sub_title' => $insertData['sub_title'],
            'category_id' => $insertData['category_id'],
            'trademark_id' => $insertData['trademark_id'],
            'duration' => $insertData['duration'],
            'saleable' => $insertData['saleable']
        ];

        $spu = ProductSpu::create($spuData);

        $detail = [
            'spu_id' => $spu->id,
            'description' => $insertData['description'],
            'images' => $insertData['images'],
            'packing_list' => $insertData['packing_list'],
            'after_service' => $insertData['after_service']
        ];

        ProductSpuDetail::create($detail);

        $specGroupIds = ProductSpecGroup::select('id')->where(['category_id' => $insertData['category_id']])->withoutGlobalScopes()->distinct()->pluck('id')->toArray();

        $pivot_product_spec_group_spu = [];
        foreach ($specGroupIds as $id) {
            $pivot_product_spec_group_spu[] = ['spec_group_id' => $id, 'spu_id' => $spu->id];
        }
        DB::table('sys_pivot_product_spec_group_spu')->insert($pivot_product_spec_group_spu);


        if (!empty($insertData['pet_breeds'])) {
            $breedSpecGroupIds = DB::table('sys_pivot_product_spec_group_value')->select('spec_group_id')->whereIn('spec_value_id', $insertData['pet_breeds'])->distinct()->pluck('spec_group_id')->toArray();

            $intersectionIds = array_intersect($specGroupIds, $breedSpecGroupIds);

            $pivot_product_spu_value = [];

            foreach ($intersectionIds as $spec_group_id) {
                foreach ($insertData['pet_breeds'] as $breed) {
                    $pivot_product_spu_value[] = [
                        'spu_id' => $spu->id,
                        'spec_group_id' => $spec_group_id,
                        'spec_value_id' => $breed
                    ];
                }
            }

            DB::table('sys_pivot_product_spu_value')->insert($pivot_product_spu_value);
        }

        return $this->message('success');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $payload = $this->service->find(['id' => $id], relations: ['detail']);

        return $this->success((new ProductSpuResource($payload))->additional(['format' => __FUNCTION__]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->service->update($id, $request->post());

        return $this->message('success');
    }

    public function batchUpdate(Request $request)
    {
        ProductSpu::whereIn('id', $request->post('ids'))->update($request->post('data'));

        return $this->message('success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->service->delete($id);

        return $this->message('success');
    }

    public function batchDestroy(Request $request)
    {
        $this->service->delete($request->post('ids'));

        return $this->message('success');
    }
}
