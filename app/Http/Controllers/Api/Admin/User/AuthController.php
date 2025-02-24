<?php

namespace App\Http\Controllers\Api\Admin\User;

use App\Enums\ResponseEnum;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'account' => ['required', 'alpha_num'],
            'password' => ['required'],
            'captcha' => ['required', 'alpha_num']
        ]);

        unset($validated['captcha']);

        $auth = Auth::guard('admin');

        if ($auth->validate($validated)) {
            if (!$auth->getLastAttempted()->status) {
                throw new BusinessException(ResponseEnum::USER_SERVICE_LOGIN_STATUS_ERROR);
            }

            $token = $auth->login($auth->getLastAttempted());

            return $this->success(compact('token'));
        } else {
            throw new BusinessException(ResponseEnum::USER_SERVICE_LOGIN_ERROR);
        }
    }

    public function logout(): \Illuminate\Http\JsonResponse
    {
        Auth::guard('admin')->logout();

        return $this->success();
    }

    public function updateSelf(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = arrHumpToLine($request->post());

        $user = Auth::guard('admin')->user();

        foreach ($validated as $key => $value) {
            if ($key !== 'reentered_password') {
                $user->{$key} = $value;
            }
            if ($key === 'password') {
                $user->{$key} = Hash::make($value);
            }
        }

        unset($user->user);

        $user->save();

        return $this->success();
    }

    public function resetPasswd(Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        $user = User::findOrFail($id);

        $user->password = Hash::make('Dcba@1234');
        $user->is_default_passwd = true;
        $user->updated_by = $validated['user'];

        $user->save();

        return $this->success();
    }

    public function info()
    {
        $payload = User::select('name', 'avatar', 'role_id', 'is_default_passwd')->with(['role' => function ($query) {
            $query->select('id', 'title')->with(['permissions' => function ($perQuery) {
                $perQuery->select('id', 'code', 'type')->wherePivotNotIn('permission_id', [1]);
            }, 'menus' => function ($perQuery) {
                $perQuery->select('id', 'code', 'type')->wherePivotNotIn('permission_id', [1]);
            }]);
        }])->where('uid', Auth::guard('admin')->id())->firstOrFail();

        return $this->success((new UserResource($payload))->additional(['format' => __FUNCTION__]));
    }


//    public function info()
//    {
//        $payload = User::select('name', 'avatar', 'role_id', 'is_default_passwd')->with(['role' => function ($query) {
//            $query->select('id', 'title')->with(['permissions' => function ($perQuery) {
//                $perQuery->select('id', 'code', 'type')->wherePivotNotIn('permission_id', [1]);
//            }]);
//        }])->where('uid', Auth::guard('admin')->id())->firstOrFail();
//
//        return $this->success((new UserResource($payload))->additional(['format' => __FUNCTION__]));
//    }


    public function authenticate(): \Illuminate\Http\JsonResponse
    {
        if (Auth::guard('admin')->check()) {
            return $this->success();
        } else {
            throw new BusinessException(ResponseEnum::CLIENT_HTTP_UNAUTHORIZED);
        }
    }

    public function index(Request $request)
    {
        $validated = arrHumpToLine($request->input());

        $payload = User::with(['role'])->when(isset($validated['name']), function ($query) use ($validated) {
            $query->where('name', 'like', '%' . $validated['name'] . '%');
        });

        $payload = $payload->paginate($validated['page_size'] ?? $this->pageSize, ['*'], 'page', $validated['page'] ?? $this->page);

        return $this->success($this->returnIndex($payload, 'UserResource', __FUNCTION__));
    }

    public function show(string $id)
    {
        $payload = User::findOrFail($id);

        return $this->success((new UserResource($payload))->additional(['format' => __FUNCTION__]));
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = arrHumpToLine($request->post());

        try {
            $user = User::where('account', $validated['account'])->orWhere('name', $validated['name'])->orWhere('phone_number', $validated['phone_number'])->firstOrFail();

            $message = match (true) {
                $user->account === $validated['account'] => '登录账号重复',
                $user->name === $validated['name'] => '用户已存在',
                $user->phone_number === $validated['phone_number'] => '手机号码重复'
            };

            throw new BusinessException(ResponseEnum::HTTP_ERROR, $message);
        } catch (ModelNotFoundException $e) {
            unset($validated['user']);

            User::create(array_merge($validated, [
                'uid' => strval(Str::ulid()),
                'password' => Hash::make('Dcba@1234'),
                'is_default_passwd' => true,
                'updated_by' => $validated['user']
            ]));
        }

        return $this->success();
    }

    public function update(Request $request, string $id)
    {
        $validated = arrHumpToLine($request->post());

        try {
            $user = User::whereNot('id', $id)->where(function ($query) use ($validated) {
                $query->where('account', $validated['account'])->orWhere('name', $validated['name'])->orWhere('phone_number', $validated['phone_number']);
            })->firstOrFail();

            $message = match (true) {
                $user->account === $validated['account'] => '登录账号重复',
                $user->name === $validated['name'] => '用户已存在',
                $user->phone_number === $validated['phone_number'] => '手机号码重复'
            };

            throw new BusinessException(ResponseEnum::HTTP_ERROR, $message);
        } catch (ModelNotFoundException $e) {
            unset($validated['user']);

            User::where('id', $id)->update(array_merge($validated, [
                'updated_by' => $validated['user']
            ]));
        }

        return $this->success();
    }

    public function batchToggle(Request $request)
    {
        $validated = arrHumpToLine($request->post());

        User::whereIn('id', $validated['ids'])->update([
            'status' => $validated['status'],
            'updated_by' => $validated['user']
        ]);

        return $this->success();
    }
}
