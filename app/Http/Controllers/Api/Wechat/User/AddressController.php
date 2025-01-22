<?php

namespace App\Http\Controllers\Api\Wechat\User;

use App\Http\Controllers\Controller;
use App\Models\ClientUserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = arrHumpToLine($request->input());
        $paginate = isset($validated['paginate']) ? isTrue($validated['paginate']) : true; // 是否分页

        $query = ClientUserAddress::select('id', 'province', 'city', 'district', 'street', 'address', 'full_address', 'person_name', 'person_phone_prefix', 'person_phone_number', 'is_default')->owner()->orderBy('is_default', 'desc')->orderBy('created_at', 'asc');

        $payload = $paginate ? $query->simplePaginate($request->get('pageSize') ?? $this->pageSize, ['*'], 'page', $request->get('page') ?? $this->page) : $query->get();

        return $this->returnIndex($payload, 'Wechat\ClientUserAddressResource', __FUNCTION__, $paginate);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = arrHumpToLine($request->input());

        if ($validated['is_default']) {
            ClientUserAddress::owner()->where('is_default', true)->update(['is_default' => false]);
        }

        $data = [
            'country' => '中国',
            'province' => $validated['province'],
            'city' => $validated['city'],
            'district' => $validated['district'],
            'street' => $validated['street'] ?? null,
            'address' => $validated['address'],
            'is_default' => $validated['is_default'],
            'person_name' => $validated['person_name'],
            'person_phone_prefix' => $validated['person_phone_prefix'],
            'person_phone_number' => $validated['person_phone_number']
        ];

        $data['full_address'] = $data['country'] . $data['province'] . $data['city'] . $data['district'] . $data['street'] . $data['address'];

        Auth::guard('wechat')->user()->addresses()->createMany([$data]);

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
        $validated = arrHumpToLine($request->post());

        $address = ClientUserAddress::owner()->findOrFail($id);

        if ($validated['is_default']) {
            ClientUserAddress::owner()->where('is_default', true)->update(['is_default' => false]);
        }

        foreach ($validated as $key => $value) {
            $address->{$key} = $value;
        }

        $address->full_address = $validated['country'] . $validated['province'] . $validated['city'] . $validated['district'] . $validated['street'] . $validated['address'];

        $address->save();

        return $this->success();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $address = ClientUserAddress::owner()->findOrFail($id);

        $address->delete();

        return $this->success();
    }
}
