<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseCollection;
use App\Services\ClientUserService;
use Illuminate\Http\Request;

class ClientUserController extends Controller
{
    public function __construct(ClientUserService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fields = ['id', 'name', 'phone_prefix', 'phone_number', 'gender', 'birthday', 'is_freeze', 'created_at', 'deleted_at'];

        $payload = $this->service->getList(fields: $fields, paginate: true);

        return (new BaseCollection($payload))->additional(['resource' => 'App\Http\Resources\ClientUserResource']);
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
}
