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
        $paginate = $request->has('paginate') ? isTrue($request->get('paginate')) : true; // 是否分页
        $cascader = $request->has('cascader') ? isTrue($request->get('cascader')) : false; // 级联选择

        $query = ProductCategory::orderBy('sort', 'asc');

        if ($request->has('trademark_id')) {
            $query = $query->whereHas('trademarks', function (Builder $q) use ($request) {
                $q->where(['id' => $request->input('trademark_id')]);
            });
        }

        if (($request->get('parent_id') ?? 0) === 0) {
            $query = $query->root();
        } else {
            $query = $query->where(['parent_id' => $request->get('parent_id')]);
        }

        if ($paginate && $cascader) {
            $query = $query->with(['trademarks', 'childrenRecursive' => function ($child) {
                $child->with(['trademarks']);
            }]);
        } elseif ($cascader) {
            $query = $query->with(['childrenRecursive']);
        }


        if ($paginate) {
            $payload = $query->paginate($request->get('pageSize') ?? $this->pageSize, ['*'], 'page', $request->get('page') ?? $this->page);
        } else {
            $payload = $query->get();
        }

        return $this->returnIndex($payload, 'ProductCategoryResource', __FUNCTION__, $paginate, $cascader);
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
        $validate = arrHumpToLine($request->post());

        if (0 !== intval($validate['id'])) {
            if (0 === ProductCategory::where(['id' => $validate['id']])->count('id')) {
                return $this->failed('非法分类，无法创建');
            }

            if (0 !== ProductCategory::where(['title' => $validate['title'], 'parent_id' => $validate['id']])->count('id')) {
                return $this->failed('当前分类已存在，请重新建立');
            }
        }

        $category = new ProductCategory();

        $category->title = $validate['title'];
        $category->sort = $validate['sort'] ?? 99;
        $category->description = $validate['description'] ?? null;
        $category->parent_id = $validate['id'];
        $category->is_parent = $validate['id'] === 0;

        $category->save();

        foreach ($validate['trademarks'] as $trademark) {
            $category->trademarks()->attach($trademark);
        }

        return $this->message('success');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validate = arrHumpToLine($request->post());

        try {
            $category = ProductCategory::with(['trademarks'])->findOrFail($id);

            if (0 === ProductCategory::where(['title' => $validate['title'], 'id' => $category->parent_id])->count('id')) {
                $category->title = $validate['title'];
                $category->sort = $validate['sort'] ?? 99;
                $category->description = $validate['description'] ?? null;

                $category->save();
                
                $category->trademarks()->detach($category->trademarks->pluck('id')->toArray());

                foreach ($validate['trademarks'] as $trademark) {
                    $category->trademarks()->attach($trademark);
                }

                return $this->message('success');
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

        return $this->message('success');
    }
}