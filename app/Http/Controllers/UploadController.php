<?php

namespace App\Http\Controllers;

use App\Services\UploadFileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    private array $storageBuildConfig;

    public function __construct(UploadFileService $service)
    {
        $this->storageBuildConfig = [
            'driver' => 'local',
            'visibility' => 'public',
            'root' => storage_path('app/public'),
            'url' => config('app.url') . DIRECTORY_SEPARATOR . 'storage'
        ];

        $this->service = $service;
    }

    public function companyInfo(Request $request)
    {
        $file = $request->file('file');

        if ($file->isValid()) {
            $this->storageBuildConfig['root'] = $this->storageBuildConfig['root'] . DIRECTORY_SEPARATOR . 'company';
            $this->storageBuildConfig['url'] = $this->storageBuildConfig['url'] . DIRECTORY_SEPARATOR . 'company';

            $disk = Storage::build($this->storageBuildConfig);

            $fileName = md5_file($file->path()) . '.' . $file->extension();

            return $this->success($this->service->uploadImage($file, $fileName, $disk));
        }

        return $this->internalError('上传文件有误，请重新上传');
    }

    public function reachText(Request $request)
    {
        $file = $request->file('file');

        if ($file->isValid()) {
            $this->storageBuildConfig['root'] = $this->storageBuildConfig['root'] . DIRECTORY_SEPARATOR . 'reachText';
            $this->storageBuildConfig['url'] = $this->storageBuildConfig['url'] . DIRECTORY_SEPARATOR . 'reachText';

            $disk = Storage::build($this->storageBuildConfig);

            $fileName = md5_file($file->path()) . '.' . $file->extension();

            // wangEditor富文本编辑器特殊要求的上传返回格式

            return response()->json([
                'errno' => 0, // 即错误代码，0 表示没有错误。如果有错误，errno != 0，可通过下文中的监听函数 fail 拿到该错误码进行自定义处理
                'data' => [ // 它们分别代表图片地址、图片文字说明和跳转链接,alt和href属性是可选的，可以不设置或设置为空字符串,需要注意的是url是一定要填的
                    'url' => $this->service->uploadImage($file, $fileName, $disk),
                    'alt' => '图片文字说明',
                    'href' => '跳转链接'
                ]
            ]);
        }

        return $this->internalError('上传文件有误，请重新上传');
    }

    public function userAvatar(Request $request)
    {
        $file = $request->file('file');

        if ($file->isValid()) {
            $this->storageBuildConfig['root'] = $this->storageBuildConfig['root'] . DIRECTORY_SEPARATOR . 'user/avatar';
            $this->storageBuildConfig['url'] = $this->storageBuildConfig['url'] . DIRECTORY_SEPARATOR . 'user/avatar';

            $disk = Storage::build($this->storageBuildConfig);

            $fileName = md5_file($file->path()) . '.' . $file->extension();

            return $this->success($this->service->uploadImage($file, $fileName, $disk));
        }

        return $this->internalError('上传文件有误，请重新上传');
    }


}