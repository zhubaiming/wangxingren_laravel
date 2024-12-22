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
        $validate = arrHumpToLine($request->input());
        $paginate = isset($validate['paginate']) ? isTrue($validate['paginate']) : true; // 是否分页

        $query = ProductSpu::with(['category', 'trademark'])->withCount(['order' => function ($order) {
            $order->where('status', OrderStatusEnum::finished);
        }])->orderBy('created_at', 'desc');

        $payload = $query->paginate($validate['page_size'] ?? $this->pageSize, ['*'], 'page', $validate['page'] ?? $this->page);

        return $this->returnIndex($payload, 'ProductSpuResource', __FUNCTION__, true);


//        $query = isset($validate['title']) ? $query->where('title', 'like', "%{$validate['title']}%") : $query;
//        $query = isset($validate['trademark_id']) ? $query->where('trademark_id', $validate['trademark_id']) : $query;
//        $query = isset($validate['category_id']) ? $query->where('category_id', $validate['category_id']) : $query;
//        $query = isset($validate['saleable']) ? $query->where('saleable', $validate['saleable']) : $query;
//
//
//        $conditions = [];
//
//        if (isset($validate['title'])) $conditions[] = ['title', 'like', "%{$validate['title']}%"];
//        if (isset($validate['category_id'])) $conditions['category_id'] = $validate['category_id'];
//        if (isset($validate['trademark_id'])) $conditions['trademark_id'] = $validate['trademark_id'];
//        if (isset($validate['saleable'])) $conditions['saleable'] = $validate['saleable'];
//
//        $relations = ['category', 'trademark'];
//
//        $payload = $this->service->getList($conditions, relations: $relations, paginate: true, page: $validate['page'] ?? $this->page, per_page: $validate['page_size']);

//        return (new BaseCollection($payload))->additional(['resource' => 'App\Http\Resources\ProductSpuResource', 'format' => __FUNCTION__]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = arrHumpToLine($request->post());

        $spuData = [
            'title' => $validate['title'],
            'sub_title' => $validate['sub_title'] ?? null,
            'trademark_id' => $validate['trademark_id'],
            'category_id' => $validate['category_id'],
//            'duration' => $validate['duration'],
            'saleable' => $validate['saleable'],
            'description' => $validate['description'] ?? null,
            'images' => $validate['images'] ?? [],
            'packing_list' => $validate['packing_list'] ?? null,
            'after_service' => $validate['after_service'] ?? null
        ];

        $spu = ProductSpu::create($spuData);

//        $detail = [
//            'spu_id' => $spu->id,
//            'description' => $validate['description'],
//            'images' => $validate['images'],
//            'packing_list' => $validate['packing_list'],
//            'after_service' => $validate['after_service']
//        ];
//
//        ProductSpuDetail::create($detail);

//        $specGroupIds = ProductSpecGroup::select('id')->where(['category_id' => $validate['category_id']])->withoutGlobalScopes()->distinct()->pluck('id')->toArray();
//
//        $pivot_product_spec_group_spu = [];
//        foreach ($specGroupIds as $id) {
//            $pivot_product_spec_group_spu[] = ['spec_group_id' => $id, 'spu_id' => $spu->id];
//        }
//        DB::table('sys_pivot_product_spec_group_spu')->insert($pivot_product_spec_group_spu);


        if (!empty($validate['pet_breeds'])) {
            $spu->spu_breed()->detach();

            $spu->spu_breed()->attach($validate['pet_breeds']);


//            $breedSpecGroupIds = DB::table('sys_pivot_product_spec_group_value')->select('spec_group_id')->whereIn('spec_value_id', $validate['pet_breeds'])->distinct()->pluck('spec_group_id')->toArray();
//
//            $intersectionIds = array_intersect($specGroupIds, $breedSpecGroupIds);
//
//            $pivot_product_spu_value = [];
//
//            foreach ($intersectionIds as $spec_group_id) {
//                foreach ($validate['pet_breeds'] as $breed) {
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

        return $this->message('success');
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
        $validate = arrHumpToLine($request->post());

        try {
            $spu = ProductSpu::findOrFail($id);

            if ($validate['category_id'] !== $spu->category_id) {
//                $specGroupIds = ProductSpecGroup::select('id')->where(['category_id' => $validate['category_id']])->withoutGlobalScopes()->distinct()->pluck('id')->toArray();
                $spu->spu_breed()->detach();
                $spu->spu_breed()->attach($validate['pet_breeds']);

                $spu->skus()->delete();
            }

            $spu->title = $validate['title'];
            $spu->sub_title = $validate['sub_title'] ?? $spu->sub_title;
            $spu->trademark_id = $validate['trademark_id'];
            $spu->category_id = $validate['category_id'];
            $spu->saleable = $validate['saleable'];
            $spu->description = $validate['description'] ?? $spu->description;
            $spu->images = $validate['images'] ?? $spu->images;
            $spu->packing_list = $validate['packing_list'] ?? $spu->packing_list;
            $spu->after_service = $validate['after_service'] ?? $spu->after_service;

            $spu->save();

            return $this->message('success');
        } catch (ModelNotFoundException $e) {
            return $this->failed('要修改的spu不存在');
        }
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
