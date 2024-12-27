<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $validate = arrHumpToLine($request->post());
        try {
            $user = User::where(['account' => $validate['account'], 'password' => $validate['password']])->firstOrFail();

            if (!$user->status) {
                return $this->failed('该账户无法登录，请联系管理员核实后再登录');
            }

            return $this->setStatusCode(201)->success(['token' => 'test']);
        } catch (ModelNotFoundException) {
            return $this->failed('用户名或密码错误', 208);
        }
    }

    public function info()
    {
        $payload = User::select('name', 'avatar', 'role_id', 'is_default_passwd')->with(['role' => function ($query) {
            $query->select('id', 'title')->with(['permissions' => function ($perQuery) {
                $perQuery->select('id', 'code', 'type')->wherePivotNotIn('permission_id', [1]);
            }]);
        }])->where('uid', Auth::guard('admin')->id())->firstOrFail();

        return $this->success((new UserResource($payload))->additional(['format' => __FUNCTION__]));
    }

    public function updateSelf(Request $request)
    {
        $validate = arrHumpToLine($request->post());

        try {
            $user = User::findOrFail(1);

            foreach ($validate as $key => $value) {
                if ($key !== 'reentered_password') {
                    $user->{$key} = $value;
                }
            }

            $user->save();

            return $this->success();
        } catch (ModelNotFoundException) {
            return $this->failed('当前用户不存在');
        }
    }

    public function logout()
    {
        return $this->message(null);
    }

    public function index(Request $request)
    {
        $validate = arrHumpToLine($request->input());

        $query = User::with(['role']);

        if (isset($validate['name'])) {
            $query = $query->where('name', 'like', "%{$request->get('name')}%");
        }

        $payload = $query->paginate($validate['page_size'] ?? $this->pageSize, ['*'], 'page', $validate['page'] ?? $this->page);

        return $this->returnIndex($payload, 'UserResource', __FUNCTION__);
    }

    public function show(string $id)
    {
        $payload = User::findOrFail($id);

        return $this->success((new UserResource($payload))->additional(['format' => __FUNCTION__]));
    }

    public function store(Request $request)
    {
        $validated = arrHumpToLine($request->post());

        $user = User::where(['account' => $validated['account']])->orWhere(['name' => $validated['name']])->orWhere(['phone_number' => $validated['phone_number']])->first();

        if (!is_null($user)) {
            if ($user->account === $validated['account']) return $this->failed('登录账号重复');
            if ($user->name === $validated['name']) return $this->failed('用户已存在');
            if ($user->phone_number === $validated['phone_number']) return $this->failed('手机号码重复');
        }

        $validated['password'] = 'Dcba@1234';
        $validated['is_default_passwd'] = true;
        $validated['updated_by'] = 'system';

        User::create($validated);

        return $this->success();
    }

    public function update(Request $request, string $id)
    {
        $validated = arrHumpToLine($request->post());

        if (isset($validated['password']) && 'Dcba@1234' !== $validated['password']) {
            $validated['is_default_passwd'] = false;
        }

        $validated['updated_by'] = 'system';

        User::where(['id' => $id])->update($validated);

        return $this->success();
    }

    public function batchToggle(Request $request)
    {
        $validated = arrHumpToLine($request->post());

        User::whereIn('id', $validated['ids'])->update(['status' => $validated['status']]);

        return $this->success();
    }

    public function resetPasswd(string $id)
    {
        User::where(['id' => $id])->update(['password' => Hash::make('Dcba@1234'), 'is_default_passwd' => true]);

        return $this->success();
    }

    public function destroy(string $id)
    {
        $this->delete($id);

        return $this->success();
    }

    private function delete(array|string $id)
    {
        User::destroy($id);
    }
}
