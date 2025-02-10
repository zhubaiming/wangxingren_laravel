<?php

namespace App\Http\Controllers\Api\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserRoleResource;
use App\Models\User;
use App\Models\UserPermission;
use App\Models\UserRole;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = arrHumpToLine($request->input());
        $paginate = isset($validated['paginate']) ? isTrue($validated['paginate']) : true; // 是否分页

        $query = new UserRole();

        if (isset($validated['title'])) {
            $query = $query->where('title', 'like', "%{$validated['title']}%");
        }

        $payload = $paginate ? $query->paginate($validated['page_size'] ?? $this->pageSize, ['*'], 'page', $validated['page'] ?? $this->page) : $query->get();

        return $this->returnIndex($payload, 'UserRoleResource', __FUNCTION__, $paginate);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = arrHumpToLine($request->post());

        if (0 === UserRole::where(['title' => $validated['title']])->count('id')) {

            $userRole = UserRole::create(['title' => $validated['title'], 'can_delete' => true, 'updated_by' => 'system']);

            if (!$userRole) {
                return $this->failed('角色创建失败');
            }

            $userRole->permissions()->attach($validated['permissions']);

            return $this->success();
        }

        return $this->failed('当前角色已存在，请重新建立');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $payload = UserRole::with(['permissions:id', 'menus' => function ($query) {
            $query->doesntHave('childrenRecursive');
        }])->findOrFail($id);

        return $this->success((new UserRoleResource($payload))->additional(['format' => __FUNCTION__]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = arrHumpToLine($request->post());

        if (0 === UserRole::where(['title' => $validated['title']])->whereNot('id', $id)->count('id')) {
            try {
                $userRole = UserRole::findOrFail($id);

                $userRole->title = $validated['title'];
                $userRole->updated_by = 'system';

                $userRole->save();

                $permissionArr = UserPermission::select('id', 'type')->whereIn('id', $validated['permissions'])->get()->toArray();

                $menu = array_filter($permissionArr, function ($item) {
                    return $item['type'] === 1;
                });

                $permission = array_filter($permissionArr, function ($item) {
                    return $item['type'] === 2;
                });

//                $noRootPermission = UserPermission::select('id', 'type')->doesntHave('childrenRecursive')->pluck('id')->toArray();
//                $userRole->permissions()->sync(array_intersect($validated['permissions'], $noRootPermission));

                $userRole->permissions()->sync(array_map(function ($item) {
                    return $item['id'];
                }, $permission));
                $userRole->menus()->sync(array_map(function ($item) {
                    return $item['id'];
                }, $menu));

                return $this->success();
            } catch (ModelNotFoundException) {
                return $this->failed('要修改的角色不存在');
            }
        }

        return $this->failed('当前角色已存在，请重新建立');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $userCount = User::where(['role_id' => $id])->count();

        if (0 !== $userCount) {
            return $this->failed('当前角色下仍有分配的用户，无法删除');
        }

        $rows = UserRole::destroy($id);

        if (0 !== $rows) {
            DB::table('pivot_role_permission')->where(['role_id' => $id])->delete();
            DB::table('pivot_role_menu')->where(['role_id' => $id])->delete();
        }

        return $this->success();
    }
}
