<?php

namespace App\Http\Controllers\Api\Admin\Product;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductSpuResource;
use App\Models\ProductSpu;
use Illuminate\Http\Request;

class SpuController extends Controller
{
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

        return $this->returnIndex($payload, 'ProductSpuResource', __FUNCTION__);
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

        if (!empty($validated['pet_breeds'])) {
            $spu->spu_breed()->detach();

            $spu->spu_breed()->attach($validated['pet_breeds']);
        }

        return $this->success();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $payload = ProductSpu::with('spu_breed')->findOrFail($id);

        return $this->success((new ProductSpuResource($payload))->additional(['format' => __FUNCTION__]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = arrHumpToLine($request->post());

        $spu = ProductSpu::with('spu_breed')->findOrFail($id);

        if ($validated['category_id'] !== $spu->category_id) { // 如果spu切换分类
            $spu->spu_breed()->detach();
            $spu->spu_breed()->attach($validated['pet_breeds']);

            $spu->skus()->delete();
        } else {
            $newBreeds = array_unique($validated['pet_breeds']);
            $origanBreeds = $spu->spu_breed->pluck('id')->toArray();

            $insertBreeds = array_diff($newBreeds, $origanBreeds);
            $deleteBreeds = array_diff($origanBreeds, $newBreeds);

            $spu->spu_breed()->detach($deleteBreeds);
            $spu->spu_breed()->attach($insertBreeds);

            if (!empty($deleteBreeds)) {
                $spu->skus()->whereIn('breed_id', $deleteBreeds)->delete();
            }
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
        $spu = ProductSpu::findOrFail($id);

        $spu->delete($id);

        return $this->success();
    }

    public function batchDestroy(Request $request)
    {
        ProductSpu::destroy($request->post('ids'));

        return $this->success();
    }
}