<?php

namespace App\Http\Controllers\Api\Wechat\Product;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\Wechat\ProductSpuResource;
use App\Models\ProductSpu;
use Illuminate\Http\Request;

class SpuController extends Controller
{
    public function searchList(Request $request)
    {
        $validated = arrHumpToLine($request->input());

        $title = $validated['title'] === 'null' || is_null($validated['title']) ? null : $validated['title'];

        $titles = ProductSpu::where('trademark_id', 325403)->where('saleable', true)
            ->when(isset($validated['category_id']), function ($query) use ($validated) {
                return $query->where('category_id', $validated['category_id']);
            })
            ->when(!is_null($title), function ($query) use ($validated) {
                return $query->where('title', 'like', '%' . $validated['title'] . '%');
            })->pluck('title')->toArray();

        $result = [];
        foreach ($titles as $key => $value) {
            $result[] = ['value' => $key, 'text' => $value];
        }

        return $this->success($result);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = arrHumpToLine($request->input());

        $title = isset($validated['title']) ? (is_null($validated['title']) || $validated['title'] === 'null' ? null : $validated['title']) : null;

        $payload = ProductSpu::where('trademark_id', 325403)->where('saleable', true)
            ->when(isset($validated['category_id']), function ($query) use ($validated) {
                return $query->where('category_id', $validated['category_id']);
            })
            ->when(!is_null($title), function ($query) use ($validated) {
                return $query->where('title', 'like', '%' . $validated['title'] . '%');
            })
            ->withMin('skus', 'price')
            ->withCount(['order' => function ($query) {
                $query->whereIn('status', OrderStatusEnum::getFinishStatuses());
            }])
            ->orderBy('sort', 'asc')
            ->orderBy('created_at', 'desc')
            ->simplePaginate($this->pageSize, ['*'], 'page', $validated['page'] ?? $this->page);

        return $this->success($this->returnIndex($payload, 'WechatService\ProductSpuResource', __FUNCTION__, true));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        $validated = arrHumpToLine($request->post());

        $payload = ProductSpu::where('trademark_id', 325403)->where('saleable', true)
            ->when(isset($validated['category_id']), function ($query) use ($validated) {
                return $query->where('category_id', $validated['category_id']);
            })
            ->with('spu_breed')
            ->withMin('skus', 'price')
            ->withCount('order')
            ->findOrFail($id);

        return $this->success((new ProductSpuResource($payload))->additional(['format' => __FUNCTION__]));
    }
}
