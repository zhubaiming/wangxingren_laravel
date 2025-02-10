<?php

namespace App\Http\Controllers\Api\Admin\Product;

use App\Enums\ResponseEnum;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductTrademarkResource;
use App\Models\ProductTrademark;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class TrademarkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = arrHumpToLine($request->input());
        $paginate = isset($validated['paginate']) ? isTrue($validated['paginate']) : true; // 是否分页

        $payload = ProductTrademark::orderBy('letter', 'asc')->when(isset($validated['title']), function ($query) use ($validated) {
            $query->where('title', 'like', '%' . $validated['title'] . '%');
        });

        $payload = $paginate ? $payload->paginate($validated['page_size'] ?? $this->pageSize, ['*'], 'page', $validated['page'] ?? $this->page) : $payload->get();

        return $this->success($this->returnIndex($payload, 'ProductTrademarkResource', __FUNCTION__, $paginate));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = arrHumpToLine($request->post());

        try {
            ProductTrademark::where(['title' => $validated['title']])->firstOrFail();

            throw new BusinessException(ResponseEnum::HTTP_ERROR, '当前品牌已存在,请重新建立');
        } catch (ModelNotFoundException $e) {
            ProductTrademark::create([
                'title' => $validated['title'],
                'letter' => isset($validated['letter']) ? strtolower($validated['letter']) : null,
                'image' => $validated['image'] ?? null,
                'description' => $validated['description'] ?? null
            ]);
        }

        return $this->success();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $payload = ProductTrademark::findOrFail($id);

        return $this->success((new ProductTrademarkResource($payload))->additional(['format' => __FUNCTION__]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = arrHumpToLine($request->post());

        try {
            ProductTrademark::whereNot('id', $id)->where(['title' => $validated['title']])->firstOrFail();

            throw new BusinessException(ResponseEnum::HTTP_ERROR, '当前品牌已存在,请重新建立');
        } catch (ModelNotFoundException $e) {
            unset($validated['user']);
            ProductTrademark::where('id', $id)->update($validated);
        }

        return $this->success();
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
