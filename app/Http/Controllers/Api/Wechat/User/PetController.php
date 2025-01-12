<?php

namespace App\Http\Controllers\Api\Wechat\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Wechat\ClientUserPetResource;
use App\Models\ClientUserPet;
use App\Models\SysPetBreed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = arrHumpToLine($request->input());
        $paginate = isset($validated['paginate']) ? isTrue($validated['paginate']) : true; // 是否分页

        $ids = isset($validated['ids']) ? explode(',', $validated['ids']) : [];

        $query = ClientUserPet::owner()
            ->when(!empty($ids), function ($query) use ($ids) {
                return $query->whereIn('id', $ids);
            })
            ->orderBy('is_default', 'desc')->orderBy('created_at', 'asc');

        $payload = $paginate ? $query->simplePaginate($request->get('pageSize') ?? $this->pageSize, ['*'], 'page', $request->get('page') ?? $this->page) : $query->get();

        return $this->returnIndex($payload, 'Wechat\ClientUserPetResource', __FUNCTION__, $paginate);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = arrHumpToLine($request->post());

        if (isset($validated['is_default']) && isTrue($validated['is_default'])) {
            ClientUserPet::owner()->where('is_default', true)->update(['is_default' => false]);
        }

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
            'is_sterilization' => isTrue($validated['is_sterilization']) ?? false,
            'is_default' => isTrue($validated['is_default']) ?? false,
            'birth' => $validated['birth'],
        ];

        Auth::guard('wechat')->user()->pets()->createMany([$data]);

        return $this->success();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $payload = ClientUserPet::owner()->findOrFail($id);

        return $this->success((new ClientUserPetResource($payload))->additional(['format' => __FUNCTION__]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = arrHumpToLine($request->post());

        $pet = ClientUserPet::owner()->findOrFail($id);

        if (isTrue($validated['is_default'])) {
            ClientUserPet::owner()->where('is_default', true)->update(['is_default' => false]);
        }

        foreach ($validated as $key => $value) {
            $pet->{$key} = $value;
        }

        $pet->save();

        return $this->success();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pet = ClientUserPet::owner()->findOrFail($id);

        $pet->delete();

        return $this->success();
    }

    public function breedIndex(string $id)
    {
        $breeds = SysPetBreed::select('id', 'title', 'letter')->where('type', $id)->get();

        $payload = [];
        foreach ($breeds as $breed) {
            $letter = $breed->letter;

            if (!isset($payload[$letter])) {
                $payload[$letter] = ['alpha' => $letter, 'sub_items' => []];
            }

            $payload[$letter]['sub_items'][] = ['id' => $breed->id, 'name' => $breed->title];
        }

        usort($payload, function ($a, $b) {
            return strcmp($a['alpha'], $b['alpha']);
        });

        $payload = array_values($payload);

        return $this->success(arrLineToHump($payload));
    }
}
