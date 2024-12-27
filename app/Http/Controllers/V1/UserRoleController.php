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
        $validate = arrHumpToLine($request->input());
        $paginate = isset($validate['paginate']) ? isTrue($validate['paginate']) : true; // 是否分页

        $query = new UserRole();

        if (isset($validate['title'])) {
            $query = $query->where('title', 'like', "%{$validate['title']}%");
        }

        $payload = $paginate ? $query->paginate($validate['page_size'] ?? $this->pageSize, ['*'], 'page', $validate['page'] ?? $this->page) : $query->get();

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

            return $this->success();
        }

        return $this->failed('当前角色已存在，请重新建立');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $payload = UserRole::with(['permissions:id'])->findOrFail($id);

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
