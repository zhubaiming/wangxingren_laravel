<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCategoryResource;
use App\Models\ProductCategory;
use App\Services\GoodsCategoryService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function __construct(GoodsCategoryService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $validated = arrHumpToLine($request->input());
        $paginate = isset($validated['paginate']) ? isTrue($validated['paginate']) : true; // 是否分页
        $cascader = isset($validated['cascader']) ? isTrue($validated['cascader']) : false; // 级联选择

        $query = ProductCategory::orderBy('sort', 'asc');

        if (isset($validated['trademark_id'])) {
            $query = $query->whereHas('trademarks', function (Builder $q) use ($validated) {
                $q->where(['id' => $validated['trademark_id']]);
            });
        }

        if (($validated['parent_id'] ?? 0) === 0) {
            $query = $query->root();
        } else {
            $query = $query->where(['parent_id' => $validated['parent_id']]);
        }

        if ($paginate && $cascader) {
            $query = $query->with(['trademarks', 'childrenRecursive' => function ($child) {
                $child->with(['trademarks']);
            }]);
        } elseif ($cascader) {
            $query = $query->with(['childrenRecursive']);
        }


        $payload = $paginate ? $query->paginate($validated['page_size'] ?? $this->pageSize, ['*'], 'page', $validated['page'] ?? $this->page) : $query->get();

        return $this->returnIndex($payload, 'ProductCategoryResource', __FUNCTION__, $paginate);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $payload = ProductCategory::with(['trademarks'])->findOrFail($id);

            return $this->success((new ProductCategoryResource($payload))->additional(['format' => __FUNCTION__]));
        } catch (ModelNotFoundException $e) {
            return $this->failed('要查询的分类不存在');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = arrHumpToLine($request->post());

        if (0 !== intval($validated['id'])) {
            if (0 === ProductCategory::where(['id' => $validated['id']])->count('id')) {
                return $this->failed('非法分类，无法创建');
            }

            if (0 !== ProductCategory::where(['title' => $validated['title'], 'parent_id' => $validated['id']])->count('id')) {
                return $this->failed('当前分类已存在，请重新建立');
            }
        }

        $category = new ProductCategory();

        $category->title = $validated['title'];
        $category->sort = $validated['sort'] ?? 99;
        $category->description = $validated['description'] ?? null;
        $category->parent_id = $validated['id'];
        $category->is_parent = $validated['id'] === 0;

        $category->save();

        foreach ($validated['trademarks'] as $trademark) {
            $category->trademarks()->attach($trademark);
        }

        return $this->success();
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = arrHumpToLine($request->post());

        try {
            $category = ProductCategory::with(['trademarks'])->findOrFail($id);

            if (0 === ProductCategory::where(['title' => $validated['title'], 'id' => $category->parent_id])->count('id')) {
                $category->title = $validated['title'];
                $category->sort = $validated['sort'] ?? 99;
                $category->description = $validated['description'] ?? null;

                $category->save();

                $category->trademarks()->detach($category->trademarks->pluck('id')->toArray());

                foreach ($validated['trademarks'] as $trademark) {
                    $category->trademarks()->attach($trademark);
                }

                return $this->success();
            }

            return $this->failed('当前分类已存在，请重新建立');

        } catch (ModelNotFoundException $e) {
            return $this->failed('要修改的分类不存在');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $category = ProductCategory::findOrFail($id);

            dd($category);

            $category->delete();
        } catch (ModelNotFoundException) {
            return $this->failed('要删除的菜单不存在');
        }

        return $this->success();
    }
}