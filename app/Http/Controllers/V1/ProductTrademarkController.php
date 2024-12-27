<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductTrademarkResource;
use App\Models\ProductTrademark;
use App\Services\ProductTrademarkService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ProductTrademarkController extends Controller
{
    public function __construct(ProductTrademarkService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $validate = arrHumpToLine($request->input());
        $paginate = isset($validate['paginate']) ? isTrue($validate['paginate']) : true; // 是否分页

        $query = ProductTrademark::orderBy('letter', 'asc');

        if (isset($validate['title'])) {
            $query = $query->where('title', 'like', "%{$validate['title']}%");
        }

        $payload = $paginate ? $query->paginate($validate['page_size'] ?? $this->pageSize, ['*'], 'page', $validate['page'] ?? $this->page) : $query->get();

        return $this->returnIndex($payload, 'ProductTrademarkResource', __FUNCTION__, $paginate);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = arrHumpToLine($request->post());

        if (0 === ProductTrademark::where(['title' => $validate['title']])->count('id')) {

            $trademark = ProductTrademark::create(['title' => $validate['title'], 'letter' => isset($validate['letter']) ? strtolower($validate['letter']) : null, 'image' => $validate['image'] ?? null, 'description' => $validate['description'] ?? null]);

            if (!$trademark) {
                return $this->failed('品牌创建失败');
            }

            return $this->success();
        }

        return $this->failed('当前品牌已存在，请重新建立');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $payload = ProductTrademark::findOrFail($id);

            return $this->success((new ProductTrademarkResource($payload))->additional(['format' => __FUNCTION__]));
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

        if (0 === ProductTrademark::where(['title' => $validate['title']])->whereNot('id', $id)->count('id')) {

            try {
                $trademark = ProductTrademark::findOrFail($id);

                foreach ($validate as $field => $value) {
                    if ($field !== 'id') {
                        $trademark->setAttribute($field, $value);
                    }
                }

                $trademark->save();

                return $this->success();
            } catch (ModelNotFoundException) {
                return $this->failed('要修改的品牌不存在');
            }
        }

        return $this->failed('当前品牌已存在，请重新建立');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        ProductTrademark::destroy($id);

        return $this->success();
    }

    public function batchDestroy(Request $request)
    {
        ProductTrademark::destroy($request->post('ids'));

        return $this->success();
    }
}