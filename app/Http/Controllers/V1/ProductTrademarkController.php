<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseCollection;
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
        $paginate = $request->get('paginate') ?? false;

        $fields = ['id', 'title', 'letter', 'image', 'created_at'];

        $payload = $this->service->getList(fields: $fields, paginate: $paginate);

        return (new BaseCollection($payload))->additional(['resource' => 'App\Http\Resources\ProductTrademarkResource', 'paginate' => $paginate]);
    }
}