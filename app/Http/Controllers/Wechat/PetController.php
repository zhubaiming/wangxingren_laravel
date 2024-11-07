<?php

namespace App\Http\Controllers\Wechat;

use App\Exceptions\WechatApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserPetRequest;
use App\Services\UploadFileService;
use App\Services\UserPetService;
use Illuminate\Support\Facades\DB;

class PetController extends Controller
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
        $payload = $this->service->pageList();
        foreach ($payload as $key => $value) {
            $payload[$key] = arrLineToHump($value);
        }
        return $this->success(data: ['data' => $payload]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserPetRequest $request)
    {
        return rescue(function () use ($request) {
            $validated = $request->validated();

            $this->service->create($validated);

            return $this->noData();
        }, function ($exception) {
            throw new WechatApiException('8848', $exception->getMessage());
        }, false);
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
    public function update(UserPetRequest $request, string $id)
    {
        $validated = $request->validated();

        $this->service->update($validated, $id);
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

        return $this->success(data: ['content' => $url]);
    }

    public function category($id)
    {
        $list = DB::table('sys_pets')->where(['type' => $id])->get();

        $payload = [];

        foreach ($list as $item) {
            $alpha = $item->alpha;
            $name = ['id' => $item->id, 'name' => $item->name];

            // 检查 $output 中是否已经有相同的 alpha
            if (!isset($payload[$alpha])) {
                // 如果没有，创建一个新的分组
                $payload[$alpha] = [
                    'alpha' => $alpha,
                    'subItems' => []
                ];
            }

            // 将当前项的 name 添加到相应的 subItems 中
            $payload[$alpha]['subItems'][] = $name;
        }

        // 使用 usort 根据 alpha 字段进行排序
        usort($payload, function ($a, $b) {
            return strcmp($a['alpha'], $b['alpha']);
        });

        // 将 $output 数组的键重置为索引数组
        $payload = array_values($payload);

        return $this->success(data: $payload);
    }

}