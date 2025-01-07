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
        $validated = arrHumpToLine($request->input());
        $paginate = isset($validated['paginate']) ? isTrue($validated['paginate']) : true; // 是否分页

        $query = ProductTrademark::orderBy('letter', 'asc');

        if (isset($validated['title'])) {
            $query = $query->where('title', 'like', "%{$validated['title']}%");
        }

        $payload = $paginate ? $query->paginate($validated['page_size'] ?? $this->pageSize, ['*'], 'page', $validated['page'] ?? $this->page) : $query->get();

        return $this->returnIndex($payload, 'ProductTrademarkResource', __FUNCTION__, $paginate);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = arrHumpToLine($request->post());

        if (0 === ProductTrademark::where(['title' => $validated['title']])->count('id')) {

            $trademark = ProductTrademark::create(['title' => $validated['title'], 'letter' => isset($validated['letter']) ? strtolower($validated['letter']) : null, 'image' => $validated['image'] ?? null, 'description' => $validated['description'] ?? null]);

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
        $validated = arrHumpToLine($request->post());

        if (0 === ProductTrademark::where(['title' => $validated['title']])->whereNot('id', $id)->count('id')) {

            try {
                $trademark = ProductTrademark::findOrFail($id);

                foreach ($validated as $field => $value) {
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