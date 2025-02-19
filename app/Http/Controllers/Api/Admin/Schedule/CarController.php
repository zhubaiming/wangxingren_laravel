<?php

namespace App\Http\Controllers\Api\Admin\Schedule;

use App\Http\Controllers\Controller;
use App\Models\ServiceCar;
use Illuminate\Http\Request;

class CarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = arrHumpToLine($request->input());
        $paginate = isset($validated['paginate']) ? isTrue($validated['paginate']) : true; // 是否分页

        $payload = ServiceCar::when(isset($validated['status']), function ($query) use ($validated) {
            $query->where('status', isTrue($validated['status']));
        })->orderBy('id', 'asc');

        $payload = $paginate ? $payload->paginate($validated['page_size'] ?? $this->pageSize, ['*'], 'page', $validated['page'] ?? $this->page) : $payload->get();

        return $this->success($this->returnIndex($payload, 'ServiceCarResource', __FUNCTION__, $paginate));
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
