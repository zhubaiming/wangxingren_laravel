<?php

namespace App\Http\Controllers\Wechat;

use App\Exceptions\WechatApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserPetRequest;
use App\Services\UserPetService;

class PetController extends Controller
{
    public function __construct(UserPetService $petService)
    {
        $this->service = $petService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->success(data: $this->service->pageList());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserPetRequest $request)
    {
        return rescue(function () use ($request) {
            $validated = $request->validated();

            $this->service->create($validated);

            return $this->noData();
        }, function ($exception) {
            throw new WechatApiException('8848', $exception->getMessage());
        }, false);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->service->info($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserPetRequest $request, string $id)
    {
        $validated = $request->validated();

        $this->service->update($validated, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->service->delete($id);
    }
}
