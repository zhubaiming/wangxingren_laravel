<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserRoleResource;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $paginate = $request->has('paginate') ? isTrue($request->get('paginate')) : true; // 是否分页

        $query = new UserRole();

        if ($request->has('title') && !is_null($request->get('title'))) {
            $query = $query->where('title', 'like', "%{$request->get('title')}%");
        }

        $payload = $paginate ? $query->paginate($request->get('pageSize') ?? $this->pageSize, ['*'], 'page', $request->get('page') ?? $this->page) : $query->get();

        return $this->returnIndex($payload, 'UserRoleResource', __FUNCTION__, $paginate);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = arrHumpToLine($request->post());

        if (0 === UserRole::where(['title' => $validate['title']])->count('id')) {

            $userRole = UserRole::create(['title' => $validate['title'], 'can_delete' => true, 'updated_by' => 'system']);

            if (!$userRole) {
                return $this->failed('角色创建失败');
            }

            $userRole->permissions()->attach($validate['permissions']);
            $userRole->menus()->attach($validate['menus']);

            return $this->message('success');
        }

        return $this->failed('当前角色已存在，请重新建立');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $payload = UserRole::with(['menus:id', 'permissions:id'])->findOrFail($id);

        return $this->success((new UserRoleResource($payload))->additional(['format' => __FUNCTION__]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validate = arrHumpToLine($request->post());

        if (0 === UserRole::where(['title' => $validate['title']])->whereNot('id', $id)->count('id')) {

            try {
                $userRole = UserRole::findOrFail($id);

                $userRole->title = $validate['title'];
                $userRole->updated_by = 'system';

                $userRole->save();

                $userRole->permissions()->sync($validate['permissions']);
                $userRole->menus()->sync($validate['menus']);

                return $this->message('success');
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

        return $this->message('success');
    }
}