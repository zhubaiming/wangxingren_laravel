<?php

namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Http\Resources\Wechat\GoodsCategoryCollection;
use App\Services\GoodsCategoryService;

class GoodCategoryController extends Controller
{
    public function __construct(GoodsCategoryService $service)
    {
        $this->service = $service;
    }

    /**
     * 获取所有分类及其子分类 或 某一父类下的所有分类及其子分类
     */
    public function index($parent_id = 0)
    {
        // 查询条件
        $conditions = ['is_parent' => 0 === $parent_id ? 1 : 0, 'parent_id' => $parent_id];

        // 关联关系
        $relations = ['childrenRecursive'];

        $payload = $this->service->getList($conditions, relations: $relations);

        return new GoodsCategoryCollection($payload);
    }
}
