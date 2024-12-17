<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ProductTrademark;
use App\Services\ProductTrademarkService;
use Illuminate\Http\Request;

class ProductTrademarkController extends Controller
{
    public function __construct(ProductTrademarkService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $paginate = $request->has('paginate') ? isTrue($request->get('paginate')) : true; // 是否分页

        $query = ProductTrademark::orderBy('letter', 'asc');

        $payload = $paginate ? $query->paginate($request->get('pageSize') ?? $this->pageSize, ['*'], 'page', $request->get('page') ?? $this->page) : $query->get();

        return $this->returnIndex($payload, 'ProductTrademarkResource', __FUNCTION__, $paginate);
    }
}