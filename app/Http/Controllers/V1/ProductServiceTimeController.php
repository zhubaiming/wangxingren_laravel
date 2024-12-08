<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseCollection;
use App\Services\ProductServiceTimeService;
use Illuminate\Http\Request;

class ProductServiceTimeController extends Controller
{
    public function __construct(ProductServiceTimeService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $payload = $this->service->getList(paginate: $request->get('paginate') ?? true);

        return (new BaseCollection($payload))->additional(['resource' => 'App\Http\Resources\ProductServiceTimeResource']);
    }
}