<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseCollection;
use App\Models\SysPetBreed;
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

    public function category_breed(string $category_id)
    {
        $payload = SysPetBreed::whereHas('specGroup', function ($q) use ($category_id) {
            $q->whereHas('category', function ($q1) use ($category_id) {
                $q1->where(['id' => $category_id]);
            });
        })->get();

        return (new BaseCollection($payload))->additional(['resource' => 'App\Http\Resources\PetBreedResource', 'format' => __FUNCTION__]);
    }
}

