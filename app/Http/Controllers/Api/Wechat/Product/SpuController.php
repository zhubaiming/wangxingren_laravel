<?php

namespace App\Http\Controllers\Api\Wechat\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\Wechat\ProductSpuResource;
use App\Models\ProductSpu;
use Illuminate\Http\Request;

class SpuController extends Controller
{
    public function searchList(Request $request)
    {
        $validate = arrHumpToLine($request->input());

        $title = $validate['title'] === 'null' || is_null($validate['title']) ? null : $validate['title'];

        $titles = ProductSpu::where('trademark_id', 325403)->where('saleable', true)
            ->when(isset($validate['category_id']), function ($query) use ($validate) {
                return $query->where('category_id', $validate['category_id']);
            })
            ->when(!is_null($title), function ($query) use ($validate) {
                return $query->where('title', 'like', '%' . $validate['title'] . '%');
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
        $validate = arrHumpToLine($request->input());

        $title = $validate['title'] === 'null' || is_null($validate['title']) ? null : $validate['title'];

        $payload = ProductSpu::where('trademark_id', 325403)->where('saleable', true)
            ->when(isset($validate['category_id']), function ($query) use ($validate) {
                return $query->where('category_id', $validate['category_id']);
            })
            ->when(!is_null($title), function ($query) use ($validate) {
                return $query->where('title', 'like', '%' . $validate['title'] . '%');
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
