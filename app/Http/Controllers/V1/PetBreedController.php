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
        $validate = arrHumpToLine($request->input());
        $paginate = isset($validate['paginate']) ? isTrue($validate['paginate']) : true; // 是否分页

        $query = SysPetBreed::orderBy('letter', 'asc')->orderBy('id', 'asc');

        if (isset($validate['type'])) {
            $query = $query->where('type', $validate['type']);
        }

        $payload = $paginate ? $query->paginate($request->get('pageSize') ?? $this->pageSize, ['*'], 'page', $request->get('page') ?? $this->page) : $query->get();

        return $this->returnIndex($payload, 'PetBreedResource', __FUNCTION__, $paginate);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = arrHumpToLine($request->post());

        if (0 === SysPetBreed::where(['title' => $validate['title']])->count('id')) {

            $userRole = SysPetBreed::create(['type' => $validate['type'], 'title' => $validate['title'], 'letter' => strtoupper($validate['letter']), 'is_sync_attr' => $validate['is_sync_attr']]);

            if (!$userRole) {
                return $this->failed('品种创建失败');
            }

//            $userRole->permissions()->attach($validate['permissions']);
            // todo:如果选择同步，需要同步到attr

            return $this->message('success');
        }

        return $this->failed('当前品种已存在，请重新建立');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $payload = SysPetBreed::findOrFail($id);

        return $this->success((new PetBreedResource($payload))->additional(['format' => __FUNCTION__]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validate = arrHumpToLine($request->post());

        if (0 === SysPetBreed::where(['title' => $validate['title']])->whereNot('id', $id)->count('id')) {

            try {
                $userRole = SysPetBreed::findOrFail($id);

                $userRole->type = $validate['type'];
                $userRole->title = $validate['title'];
                $userRole->letter = strtoupper($validate['letter']);
                $userRole->is_sync_attr = $validate['is_sync_attr'];

                $userRole->save();

//                $userRole->permissions()->sync($validate['permissions']);
                // todo:如果选择同步，需要同步到attr

                return $this->message('success');
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
            if ($breed->is_sync_attr) {

            }

            $breed->delete();

            return $this->message('success');
        } catch (ModelNotFoundException) {
            return $this->failed('要删除的品种不存在');
        }
    }

    public function category_breed(string $category_id)
    {
        $payload = SysPetBreed::whereHas('specGroup', function ($q) use ($category_id) {
            $q->whereHas('category', function ($q1) use ($category_id) {
                $q1->where(['id' => $category_id]);
            });
        })->get();

        return $this->returnIndex($payload, 'PetBreedResource', __FUNCTION__, false);
    }
}

