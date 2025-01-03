<?php

namespace App\Http\Controllers\Api\Wechat\User;

use App\Http\Controllers\Controller;
use App\Models\ClientUserPet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validate = arrHumpToLine($request->input());
        $paginate = isset($validate['paginate']) ? isTrue($validate['paginate']) : true; // 是否分页

        $query = ClientUserPet::where(['user_id' => Auth::guard('wechat')->user()->id])->orderBy('is_default', 'desc')->orderBy('created_at', 'asc');

        $payload = $paginate ? $query->simplePaginate($request->get('pageSize') ?? $this->pageSize, ['*'], 'page', $request->get('page') ?? $this->page) : $query->get();

        return $this->returnIndex($payload, 'Wechat\ClientUserPetResource', __FUNCTION__, $paginate);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = arrHumpToLine($request->post());

        $data = [
            'breed_id' => $validated['breed_id'],
            'breed_title' => $validated['breed_title'],
            'name' => $validated['name'],
            'breed_type' => $validated['breed_type'],
            'gender' => $validated['gender'],
            'weight' => $validated['weight'],
            'color' => $validated['color'] ?? null,
            'avatar' => $validated['avatar'] ?? null,
            'remark' => $validated['remark'] ?? null,
            'is_sterilization' => $validated['is_sterilization'] ?? false,
            'is_default' => $validated['is_default'] ?? false,
            'birth' => $validated['birth'],
            'age' => $validated['birth'],
            'weight_id' => $validated['weight']
        ];

        Auth::guard('wechat')->user()->pets()->createMany([$data]);

        return $this->success();
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
