<?php

namespace App\Http\Controllers\Api\Admin\ClientUser;

use App\Http\Controllers\Controller;
use App\Models\ClientUserAddress;
use App\Models\SysArea;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = arrHumpToLine($request->input());
        $paginate = isset($validated['paginate']) ? isTrue($validated['paginate']) : true; // 是否分页

        $query = ClientUserAddress::where('user_id', $validated['user_id'])->orderBy('id', 'asc');

        $payload = $paginate ? $query->paginate($request->get('pageSize') ?? $this->pageSize, ['*'], 'page', $request->get('page') ?? $this->page) : $query->get();

        return $this->returnIndex($payload, 'ClientUserAddressResource', __FUNCTION__, $paginate);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = arrHumpToLine($request->input());

        $area = SysArea::select('name_path')->where('code', $validated['full_area'])->firstOrFail();
        $area = explode('/', $area->name_path);

        ClientUserAddress::create([
            'user_id' => $validated['user_id'],
            'country' => $area[0],
            'province' => $area[1],
            'city' => $area[2],
            'district' => $area[3],
            'street' => null,
            'address' => $validated['address'],
            'is_default' => false,
            'person_name' => $validated['person_name'],
            'person_phone_prefix' => '86',
            'person_phone_number' => $validated['person_phone_number']
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
