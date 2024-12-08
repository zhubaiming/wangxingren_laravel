<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseCollection;
use App\Services\PetBreedService;
use Illuminate\Http\Request;

class PetBreedController extends Controller
{
    public function __construct(PetBreedService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $category = $request->get('category');

        $categorys = explode('-', $category);

        // 查询条件
        $conditions = ['type' => $categorys[count($categorys) - 1] === '1426' ? 1 : 2];

        $payload = $this->service->getList($conditions);

        return (new BaseCollection($payload))->additional(['resource' => 'App\Http\Resources\PetBreedResource']);
    }
}