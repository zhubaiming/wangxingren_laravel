<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ProductAttr;
use Illuminate\Http\Request;

class ProductAttrController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validate = arrHumpToLine($request->input());
        $paginate = isset($validate['paginate']) ? isTrue($validate['paginate']) : true; // 是否分页

        $query = ProductAttr::with('category')->orderBy('id', 'asc');

        if (isset($validate['category_id'])) {
            $query = $query->where('category_id', $validate['category_id']);
        }

        $payload = $paginate ? $query->paginate($validate['page_size'] ?? $this->pageSize, ['*'], 'page', $validate['page'] ?? $this->page) : $query->get();

        return $this->returnIndex($payload, 'ProductAttrResource', __FUNCTION__, $paginate);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function category_attr(string $category_id)
    {
        $payload = ProductAttr::whereHas('category', function ($query) use ($category_id) {
            $query->where('category_id', $category_id);
        })->get();

        return $this->returnIndex($payload, 'ProductAttrResource', __FUNCTION__, false);
    }
}
