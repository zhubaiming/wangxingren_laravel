<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PetBreedResource;
use App\Models\SysPetBreed;
use App\Services\PetBreedService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PetBreedController extends Controller
{
    public function __construct(PetBreedService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $validated = arrHumpToLine($request->input());
        $paginate = isset($validated['paginate']) ? isTrue($validated['paginate']) : true; // 是否分页

        $query = SysPetBreed::orderBy('letter', 'asc')->orderBy('id', 'asc');

        if (isset($validated['type'])) {
            $query = $query->where('type', $validated['type']);
        }

        $payload = $paginate ? $query->paginate($request->get('pageSize') ?? $this->pageSize, ['*'], 'page', $request->get('page') ?? $this->page) : $query->get();

        return $this->returnIndex($payload, 'PetBreedResource', __FUNCTION__, $paginate);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = arrHumpToLine($request->post());

        if (0 === SysPetBreed::where(['title' => $validated['title']])->count('id')) {

            $data = [
                'type' => $validated['type'],
                'title' => $validated['title'],
                'letter' => strtoupper($validated['letter']),
                'product_trademark_id' => strtoupper($validated['product_trademark_id']),
                'product_category_id' => strtoupper($validated['product_category_id']),
//                'is_sync_attr' => $validated['is_sync_attr']
            ];

//            if (isTrue($validated['is_sync_attr'])) {
//                $data['sync_product_trademark_id'] = $validated['sync_product_trademark_id'];
//                $data['sync_product_category_id'] = $validated['sync_product_category_id'];
//            }

            $breed = SysPetBreed::create($data);

            if (!$breed) {
                return $this->failed('品种创建失败');
            }

//            if (isTrue($validated['is_sync_attr'])) {
//                $breed->attrs()->attach($validated['sync_product_attr_id']);
//            }


            return $this->success();
        }

        return $this->failed('当前品种已存在，请重新建立');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
//        $payload = SysPetBreed::with('attrs')->findOrFail($id);
        $payload = SysPetBreed::findOrFail($id);

        return $this->success((new PetBreedResource($payload))->additional(['format' => __FUNCTION__]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = arrHumpToLine($request->post());

        if (0 === SysPetBreed::where(['title' => $validated['title']])->whereNot('id', $id)->count('id')) {

            try {
                $breed = SysPetBreed::findOrFail($id);

                $breed->type = $validated['type'];
                $breed->title = $validated['title'];
                $breed->letter = strtoupper($validated['letter']);
                $breed->product_trademark_id = $validated['product_trademark_id'];
                $breed->product_category_id = $validated['product_category_id'];

//                $breed->is_sync_attr = $validated['is_sync_attr'];
//                $breed->sync_product_trademark_id = null;
//                $breed->sync_product_category_id = null;
//
//                $breed->attrs()->detach();
//
//                if (isTrue($validated['is_sync_attr'])) {
//                    $breed->attrs()->attach($validated['sync_product_attr_id']);
//
//                    $breed->sync_product_trademark_id = $validated['sync_product_trademark_id'];
//                    $breed->sync_product_category_id = $validated['sync_product_category_id'];
//                }

                $breed->save();

                return $this->success();
            } catch (ModelNotFoundException) {
                return $this->failed('要修改的品种不存在');
            }
        }

        return $this->failed('当前品种已存在，请重新建立');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $breed = SysPetBreed::findOrFail($id);

            //todo:如果数据已经同步，需要删除同步的数据
//            if ($breed->is_sync_attr) {
//                $breed->attrs()->detach();
//            }

            $breed->delete();

            return $this->success();
        } catch (ModelNotFoundException) {
            return $this->failed('要删除的品种不存在');
        }
    }

    public function category_breed(string $category_id)
    {
//        $payload = SysPetBreed::whereHas('specGroup', function ($query) use ($category_id) {
//            $query->whereHas('category', function ($withQuery) use ($category_id) {
//                $withQuery->where(['id' => $category_id]);
//            });
//        })->get();

        $payload = SysPetBreed::where('product_category_id', $category_id)->get();

        return $this->returnIndex($payload, 'PetBreedResource', __FUNCTION__, false);
    }

    public function spu_breed(string $spu_id)
    {
        $payload = SysPetBreed::whereHas('spu', function ($spu) use ($spu_id) {
            $spu->where('id', $spu_id);
        })->get();

//        dd($payload->toArray());

        return $this->returnIndex($payload, 'PetBreedResource', __FUNCTION__, false);
    }
}

