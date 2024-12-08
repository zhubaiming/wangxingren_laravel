<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseCollection;
use App\Services\PetBreedWeightService;
use Illuminate\Http\Request;

class PetWeightController extends Controller
{
    public function __construct(PetBreedWeightService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        // 查询条件
        $conditions = ['in' => ['breed_id', $request->get('breedIds')]];

        $relations = ['breed'];

        $payload = $this->service->getList($conditions, relations: $relations);

//        dd($payload);
        dd((new BaseCollection($payload))->additional(['resource' => 'App\Http\Resources\PetBreedWeightResource']));

        return (new BaseCollection($payload))->additional(['resource' => 'App\Http\Resources\PetBreedWeightResource']);
    }
}