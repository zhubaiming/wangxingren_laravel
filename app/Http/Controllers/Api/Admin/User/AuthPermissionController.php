<?php

namespace App\Http\Controllers\Api\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserPermissionResource;
use App\Models\UserPermission;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class AuthPermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = arrHumpToLine($request->input());
        $paginate = isset($validated['paginate']) ? isTrue($validated['paginate']) : true; // 是否分页

        $payload = UserPermission::where(['level' => 1])->with(['childrenRecursive'])->orderBy('sort', 'asc')->get();

        return $this->returnIndex($payload, 'UserPermissionResource', __FUNCTION__, $paginate);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = arrHumpToLine($request->post());

        $parentPermission = UserPermission::select('level', 'type')->findOrFail($validated['id']);

        $userPermission = UserPermission::create([
            'title' => $validated['title'],
            'level' => intval(bcadd($parentPermission->level, '1', 0)),
            'parent_id' => $validated['id'],
            'code' => $validated['code'],
            'type' => 3 === $parentPermission->level ? 2 : 1,
            'select' => true,
            'sort' => $validated['sort']
        ]);

        if ($userPermission->type === 1) {
            $userPermission->menus()->attach(1);
        } else {
            $userPermission->permissions()->attach(1);
        }

        return $this->success();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $payload = UserPermission::findOrFail($id);

        return $this->success((new UserPermissionResource($payload))->additional(['format' => __FUNCTION__]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = arrHumpToLine($request->post());

        $userPermission = UserPermission::findOrFail($id);

        $userPermission->title = $validated['title'];
        $userPermission->code = $validated['code'];
        $userPermission->sort = $validated['sort'];

        $userPermission->save();

        return $this->success();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $userPermission = UserPermission::findOrFail($id);

            if ($userPermission->type === 1) {
                $userPermission->menus()->detach();
            } else {
                $userPermission->permissions()->detach();
            }

            $userPermission->delete();
        } catch (ModelNotFoundException) {
            return $this->failed('要删除的菜单不存在');
        }

        return $this->success();
    }
}
