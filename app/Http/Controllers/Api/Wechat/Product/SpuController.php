<?php

namespace App\Http\Controllers\Api\Wechat\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\Wechat\ProductSpuResource;
use App\Models\ProductSpu;
use Illuminate\Http\Request;

class SpuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validate = arrHumpToLine($request->input());

        $payload = ProductSpu::where('trademark_id', 325403)->where('saleable', true)
            ->when(isset($validate['category_id']), function ($query) use ($validate) {
                return $query->where('category_id', $validate['category_id']);
            })
            ->withMin('skus', 'price')
            ->withCount('order')
            ->orderBy('created_at', 'desc')
            ->simplePaginate($this->pageSize, ['*'], 'page', $validate['page'] ?? $this->page);

        return $this->returnIndex($payload, 'Wechat\ProductSpuResource', __FUNCTION__, true);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        $validate = arrHumpToLine($request->post());

        $payload = ProductSpu::where('trademark_id', 325403)->where('saleable', true)
            ->when(isset($validate['category_id']), function ($query) use ($validate) {
                return $query->where('category_id', $validate['category_id']);
            })
            ->with('spu_breed')
            ->withMin('skus', 'price')
            ->withCount('order')
            ->findOrFail($id);

        return $this->success((new ProductSpuResource($payload))->additional(['format' => __FUNCTION__]));
    }
}
