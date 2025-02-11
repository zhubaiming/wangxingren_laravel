<?php

namespace App\Http\Controllers\Api\Admin\ClientUser;

use App\Http\Controllers\Controller;
use App\Models\ClientUserPet;
use App\Models\ProductSpu;
use App\Models\SysPetBreed;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = arrHumpToLine($request->input());
        $paginate = isset($validated['paginate']) ? isTrue($validated['paginate']) : true; // 是否分页

        $breeds = isset($validated['spu_id']) ? ProductSpu::with('spu_breed')->findOrFail($validated['spu_id'])->spu_breed->pluck('id')->toArray() : [];

        $query = ClientUserPet::where('user_id', $validated['user_id'])
            ->when(isset($validated['spu_id']), function ($breed) use ($breeds) {
                $breed->whereIn('breed_id', $breeds);
            })
            ->orderBy('id', 'asc');

        $payload = $paginate ? $query->paginate($request->get('pageSize') ?? $this->pageSize, ['*'], 'page', $request->get('page') ?? $this->page) : $query->get();

        return $this->returnIndex($payload, 'ClientUserPetResource', __FUNCTION__, $paginate);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = arrHumpToLine($request->input());

        $petBreed = SysPetBreed::select('title')->findOrFail($validated['breed_id']);

        ClientUserPet::create([
            'user_id' => $validated['user_id'],
            'breed_id' => $validated['breed_id'],
            'breed_title' => $petBreed->title,
            'name' => $validated['name'],
            'breed_type' => $validated['breed_type'],
            'gender' => $validated['gender'],
            'weight' => $validated['weight'],
            'color' => $validated['color'] ?? null,
            'avatar' => null,
            'remark' => $validated['remark'] ?? null,
            'is_sterilization' => isTrue($validated['is_sterilization']) ?? false,
            'is_default' => false,
            'birth' => Carbon::createFromTimeStamp($validated['birth'] / 1000, config('app.timezone'))->format('Y-m'),
        ]);

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
