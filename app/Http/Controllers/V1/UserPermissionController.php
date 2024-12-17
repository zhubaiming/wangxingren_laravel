<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserPermissionResource;
use App\Models\UserPermission;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class UserPermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $paginate = $request->has('paginate') ? isTrue($request->get('paginate')) : true; // 是否分页

        $payload = UserPermission::where(['level' => 1])->with(['childrenRecursive'])->orderBy('sort', 'asc')->get();

        return $this->returnIndex($payload, 'UserPermissionResource', __FUNCTION__, $paginate);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = arrHumpToLine($request->post());

        try {
            $parentPermisstion = UserPermission::select('level', 'type')->findOrFail($validate['id']);

            $userPermisstion = UserPermission::create([
                'title' => $validate['title'],
                'level' => intval(bcadd($parentPermisstion->level, '1', 0)),
                'parent_id' => $validate['id'],
                'code' => $validate['code'],
                'type' => 3 === $parentPermisstion->level ? 2 : 1,
                'select' => true,
                'sort' => $validate['sort']
            ]);

            $userPermisstion->roles()->attach(1);
        } catch (ModelNotFoundException) {
            return $this->failed('非法菜单，无法创建');
        }

        return $this->message('success');
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
        $validate = arrHumpToLine($request->post());

        try {
            $userPermisstion = UserPermission::findOrFail($id);

            $userPermisstion->title = $validate['title'];
            $userPermisstion->code = $validate['code'];
            $userPermisstion->sort = $validate['sort'];

            $userPermisstion->save();
        } catch (ModelNotFoundException) {
            return $this->failed('要修改的菜单不存在');
        }

        return $this->message('success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $userPermisstion = UserPermission::findOrFail($id);

            $userPermisstion->delete();
        } catch (ModelNotFoundException) {
            return $this->failed('要删除的菜单不存在');
        }

        return $this->message('success');
    }
}
