<?php

namespace App\Http\Controllers\V1;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductSpuResource;
use App\Models\ProductSpecGroup;
use App\Models\ProductSpu;
use App\Services\ProductSpuService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
    public function index(Request $request)
    {
        $validated = arrHumpToLine($request->input());
        $paginate = isset($validated['paginate']) ? isTrue($validated['paginate']) : true; // 是否分页

        $query = ProductSpu::with(['category', 'trademark'])->withCount(['order' => function ($order) {
            $order->where('status', OrderStatusEnum::finished);
        }])->orderBy('created_at', 'desc');

        $payload = $query->paginate($validated['page_size'] ?? $this->pageSize, ['*'], 'page', $validated['page'] ?? $this->page);

        return $this->returnIndex($payload, 'ProductSpuResource', __FUNCTION__, true);


//        $query = isset($validated['title']) ? $query->where('title', 'like', "%{$validated['title']}%") : $query;
//        $query = isset($validated['trademark_id']) ? $query->where('trademark_id', $validated['trademark_id']) : $query;
//        $query = isset($validated['category_id']) ? $query->where('category_id', $validated['category_id']) : $query;
//        $query = isset($validated['saleable']) ? $query->where('saleable', $validated['saleable']) : $query;
//
//
//        $conditions = [];
//
//        if (isset($validated['title'])) $conditions[] = ['title', 'like', "%{$validated['title']}%"];
//        if (isset($validated['category_id'])) $conditions['category_id'] = $validated['category_id'];
//        if (isset($validated['trademark_id'])) $conditions['trademark_id'] = $validated['trademark_id'];
//        if (isset($validated['saleable'])) $conditions['saleable'] = $validated['saleable'];
//
//        $relations = ['category', 'trademark'];
//
//        $payload = $this->service->getList($conditions, relations: $relations, paginate: true, page: $validated['page'] ?? $this->page, per_page: $validated['page_size']);

//        return (new BaseCollection($payload))->additional(['resource' => 'App\Http\Resources\ProductSpuResource', 'format' => __FUNCTION__]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = arrHumpToLine($request->post());

        $spuData = [
            'title' => $validated['title'],
            'sub_title' => $validated['sub_title'] ?? null,
            'trademark_id' => $validated['trademark_id'],
            'category_id' => $validated['category_id'],
//            'duration' => $validated['duration'],
            'saleable' => $validated['saleable'],
            'description' => $validated['description'] ?? null,
            'images' => $validated['images'] ?? [],
            'packing_list' => $validated['packing_list'] ?? null,
            'after_service' => $validated['after_service'] ?? null
        ];

        $spu = ProductSpu::create($spuData);

//        $detail = [
//            'spu_id' => $spu->id,
//            'description' => $validated['description'],
//            'images' => $validated['images'],
//            'packing_list' => $validated['packing_list'],
//            'after_service' => $validated['after_service']
//        ];
//
//        ProductSpuDetail::create($detail);

//        $specGroupIds = ProductSpecGroup::select('id')->where(['category_id' => $validated['category_id']])->withoutGlobalScopes()->distinct()->pluck('id')->toArray();
//
//        $pivot_product_spec_group_spu = [];
//        foreach ($specGroupIds as $id) {
//            $pivot_product_spec_group_spu[] = ['spec_group_id' => $id, 'spu_id' => $spu->id];
//        }
//        DB::table('sys_pivot_product_spec_group_spu')->insert($pivot_product_spec_group_spu);


        if (!empty($validated['pet_breeds'])) {
            $spu->spu_breed()->detach();

            $spu->spu_breed()->attach($validated['pet_breeds']);


//            $breedSpecGroupIds = DB::table('sys_pivot_product_spec_group_value')->select('spec_group_id')->whereIn('spec_value_id', $validated['pet_breeds'])->distinct()->pluck('spec_group_id')->toArray();
//
//            $intersectionIds = array_intersect($specGroupIds, $breedSpecGroupIds);
//
//            $pivot_product_spu_value = [];
//
//            foreach ($intersectionIds as $spec_group_id) {
//                foreach ($validated['pet_breeds'] as $breed) {
//                    $pivot_product_spu_value[] = [
//                        'spu_id' => $spu->id,
//                        'spec_group_id' => $spec_group_id,
//                        'spec_value_id' => $breed
//                    ];
//                }
//            }
//
//            DB::table('sys_pivot_product_spu_value')->insert($pivot_product_spu_value);
        }

        return $this->success();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
//            $payload = ProductSpu::with('attr')->findOrFail($id);
            $payload = ProductSpu::with('spu_breed')->findOrFail($id);

            return $this->success((new ProductSpuResource($payload))->additional(['format' => __FUNCTION__]));
        } catch (ModelNotFoundException $e) {
            return $this->failed('未找到数据');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = arrHumpToLine($request->post());

        try {
            $spu = ProductSpu::findOrFail($id);

            if ($validated['category_id'] !== $spu->category_id) {
//                $specGroupIds = ProductSpecGroup::select('id')->where(['category_id' => $validated['category_id']])->withoutGlobalScopes()->distinct()->pluck('id')->toArray();
                $spu->spu_breed()->detach();
                $spu->spu_breed()->attach($validated['pet_breeds']);

                $spu->skus()->delete();
            }

            $spu->title = $validated['title'];
            $spu->sub_title = $validated['sub_title'] ?? $spu->sub_title;
            $spu->trademark_id = $validated['trademark_id'];
            $spu->category_id = $validated['category_id'];
            $spu->saleable = $validated['saleable'];
            $spu->description = $validated['description'] ?? $spu->description;
            $spu->images = $validated['images'] ?? $spu->images;
            $spu->packing_list = $validated['packing_list'] ?? $spu->packing_list;
            $spu->after_service = $validated['after_service'] ?? $spu->after_service;

            $spu->save();

            return $this->success();
        } catch (ModelNotFoundException $e) {
            return $this->failed('要修改的spu不存在');
        }
    }

    public function batchUpdate(Request $request)
    {
        ProductSpu::whereIn('id', $request->post('ids'))->update($request->post('data'));

        return $this->success();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->service->delete($id);

        return $this->success();
    }

    public function batchDestroy(Request $request)
    {
        $this->service->delete($request->post('ids'));

        return $this->success();
    }
}
