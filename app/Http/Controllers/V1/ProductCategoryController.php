<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseCollection;
use App\Models\ProductCategory;
use App\Services\GoodsCategoryService;
use Illuminate\Database\Eloquent\Builder;
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
        $cascader = $request->has('cascader') ? isTrue($request->get('cascader')) : true; // 级联选择

//        $query = new ProductCategory();
        $query = ProductCategory::orderBy('sort', 'asc');

        if ($request->has('trademark_id')) {
            $query = $query->whereHas('trademark', function (Builder $q) use ($request) {
                $q->where(['id' => $request->input('trademark_id')]);
            });
        }

        if (($request->get('parent_id') ?? 0) === 0) {
            $query = $query->root();
        } else {
            $query = $query->where(['parent_id' => $request->get('parent_id')]);
        }

        if ($cascader) {
            $query = $query->with(['childrenRecursive']);
        }

        if ($paginate) {
            $payload = $query->paginate($request->get('pageSize') ?? $this->pageSize, ['*'], 'page', $request->get('page') ?? $this->page);
        } else {
            $payload = $query->get();
        }

        return $this->returnIndex($payload, 'ProductCategoryResource', __FUNCTION__, $paginate);
//        return (new BaseCollection($payload))->additional(['resource' => 'App\Http\Resources\ProductCategoryResource', 'paginate' => $paginate, 'cascader' => $cascader]);
    }
}