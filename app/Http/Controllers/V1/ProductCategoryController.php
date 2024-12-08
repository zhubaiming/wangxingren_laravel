<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseCollection;
use App\Services\GoodsCategoryService;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function __construct(GoodsCategoryService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $paginate = $request->get('paginate') ?? false;

        // 作用域
        $scopes = ['root']; // 调用 popular 作用域

        // 关联关系
        $relations = ['childrenRecursive'];

        $payload = $this->service->getList(scopes: $scopes, relations: $relations, paginate: $paginate);

//        dd($payload);

        return (new BaseCollection($payload))->additional(['resource' => 'App\Http\Resources\ProductCategoryResource', 'paginate' => $paginate]);
    }
}