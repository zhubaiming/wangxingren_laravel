<?php

namespace App\Http\Controllers\Api\Admin\ClientUser;

use App\Http\Controllers\Controller;
use App\Models\ClientUser;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = arrHumpToLine($request->input());
        $paginate = isset($validated['paginate']) ? isTrue($validated['paginate']) : true; // 是否分页

//        $query = ClientUser::where('is_freeze', false)->orderBy('id', 'asc');

        $payload = ClientUser::when(isset($validated['name']), function ($query) use ($validated) {
            $query->where('name', 'like', '%' . $validated['name'] . '%');
        })
            ->when(isset($validated['phone']), function ($query) use ($validated) {
                $query->where('phone_number', 'like', '%' . $validated['phone'] . '%');
            })
            ->when(isset($validated['gender']), function ($query) use ($validated) {
                $query->where('gender', $validated['gender']);
            })
//            ->when(isset($validated['status']), function ($query) use ($validated) {
//                $query->where('gender', $validated['gender']);
//            })
            ->orderBy('id', 'asc');

//        $payload = $paginate ? $payload->paginate($request->get('page_size') ?? $this->pageSize, ['*'], 'page', $request->get('page') ?? $this->page) : $query->get();
        $payload = $paginate ? $payload->paginate($validated['page_size'] ?? $this->pageSize, ['*'], 'page', $validated['page'] ?? $this->page) : $paginate->get();

        return $this->success($this->returnIndex($payload, 'ClientUserResource', __FUNCTION__, $paginate));
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
