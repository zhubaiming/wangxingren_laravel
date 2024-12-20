<?php

namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserPetRequest;
use App\Http\Resources\BaseCollection;
use App\Services\UploadFileService;
use App\Services\UserPetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPetController extends Controller
{
    public function __construct(UserPetService $petService)
    {
        $this->service = $petService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 作用域
        $scopes = ['owner' => true]; // 调用 popular 作用域

        $payload = $this->service->getList(scopes: $scopes);

        return (new BaseCollection($payload))->additional(['resource' => 'App\Http\Resources\Wechat\UserPetResource']);
    }

    /**
     * Store a newly created resource in storage.
     */
//    public function store(UserPetRequest $request)
    public function store(Request $request)
    {
        $validated = arrHumpToLine($request->post());

        $data = [
            'breed_id' => $validated['breed_id'],
            'breed_title' => $validated['breed_title'],
            'name' => $validated['name'],
            'breed_type' => $validated['breed_type'],
            'gender' => $validated['gender'],
            'weight' => $validated['weight'],
            'color' => $validated['color'] ?? null,
            'avatar' => $validated['avatar'] ?? null,
            'remark' => $validated['remark'] ?? null,
            'is_sterilization' => $validated['is_sterilization'] ?? false,
            'is_default' => $validated['is_default'] ?? false,
            'birth' => $validated['birth'],
            'age' => $validated['birth'],
            'weight_id' => $validated['weight']
        ];


        Auth::guard('wechat')->user()->pets()->createMany([$data]);

        return $this->message('success');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->service->info($id);
    }

    /**
     * Update the specified resource in storage.
     */
//    public function update(UserPetRequest $request, string $id)
    public function update(Request $request, string $id)
    {

//        dd($request, $id);
//        $validated = $request->validated();

        $validated = arrHumpToLine($request->post());

        $this->service->update($validated, $id);

        return $this->message();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->service->delete($id);
    }

    public function upload(UserPetRequest $request, UploadFileService $service)
    {
        $validated = $request->validated();

        if (is_null($url = $service->petAvatar($validated['avatar']))) {
            dd('错误');
        }

        return $this->success(['content' => $url]);
    }

    public function category($id)
    {
        $list = $this->service->getCategoryList($id);

        $payload = [];

        foreach ($list as $item) {
            $letter = $item->letter;
            $name = ['id' => $item->id, 'name' => $item->title];

            // 检查 $output 中是否已经有相同的 alpha
            if (!isset($payload[$letter])) {
                // 如果没有，创建一个新的分组
                $payload[$letter] = [
                    'alpha' => $letter,
                    'subItems' => []
                ];
            }

            // 将当前项的 name 添加到相应的 subItems 中
            $payload[$letter]['subItems'][] = $name;
        }

        // 使用 usort 根据 alpha 字段进行排序
        usort($payload, function ($a, $b) {
            return strcmp($a['alpha'], $b['alpha']);
        });

        // 将 $output 数组的键重置为索引数组
        $payload = array_values($payload);

        return $this->success($payload);
    }

}