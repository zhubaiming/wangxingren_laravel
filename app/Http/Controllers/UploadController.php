<?php

namespace App\Http\Controllers;

use App\Enums\ResponseEnum;
use App\Exceptions\BusinessException;
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
            'url' => config('app.url') . DIRECTORY_SEPARATOR . 'storage',
            'permissions' => [
                'file' => [
                    'public' => 0644,
                    'private' => 0600
                ],
                'dir' => [
                    'public' => 0755,
                    'private' => 0700
                ]
            ],
        ];

        $this->service = $service;
    }

    private function storageSave(\SplFileInfo $file): string
    {
        if ($file->isValid()) {
            $disk = Storage::build($this->storageBuildConfig);

            $fileName = md5_file($file->path()) . '.' . $file->extension();

            return $this->service->uploadImage($file, $fileName, $disk);
        }

        throw new BusinessException(ResponseEnum::HTTP_ERROR, '上传文件有误，请重新上传');
    }

    public function reachText(Request $request)
    {
        $file = $request->file('file');

        $this->storageBuildConfig['root'] = $this->storageBuildConfig['root'] . DIRECTORY_SEPARATOR . 'ReachText';
        $this->storageBuildConfig['url'] = $this->storageBuildConfig['url'] . DIRECTORY_SEPARATOR . 'ReachText';

        $url = $this->storageSave($file);

        // wangEditor富文本编辑器特殊要求的上传返回格式

        return response()->json([
            'errno' => 0, // 即错误代码，0 表示没有错误。如果有错误，errno != 0，可通过下文中的监听函数 fail 拿到该错误码进行自定义处理
            'data' => [ // 它们分别代表图片地址、图片文字说明和跳转链接,alt和href属性是可选的，可以不设置或设置为空字符串,需要注意的是url是一定要填的
                'url' => $url, 'alt' => '图片文字说明', 'href' => '跳转链接']
        ]);
    }

    public function appBanner(Request $request)
    {
        $file = $request->file('file');

        $dirPath = match (true) {
            strpos($file->getClientMimeType(), 'image/') === 0 => 'Image',
            strpos($file->getClientMimeType(), 'video/') === 0 => 'Video',
            default => 'other'
        };

        $this->storageBuildConfig['root'] = $this->storageBuildConfig['root'] . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Banner' . DIRECTORY_SEPARATOR . $dirPath;
        $this->storageBuildConfig['url'] = $this->storageBuildConfig['url'] . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Banner' . DIRECTORY_SEPARATOR . $dirPath;

        $url = $this->storageSave($file);

        return $this->success($url);
    }

    public function spuImages(Request $request)
    {
        $file = $request->file('file');

        $this->storageBuildConfig['root'] = $this->storageBuildConfig['root'] . DIRECTORY_SEPARATOR . 'Spu';
        $this->storageBuildConfig['url'] = $this->storageBuildConfig['url'] . DIRECTORY_SEPARATOR . 'Spu';

        $url = $this->storageSave($file);

        return $this->success($url);
    }

    public function userAvatar(Request $request)
    {
        $file = $request->file('file');

        $this->storageBuildConfig['root'] = $this->storageBuildConfig['root'] . DIRECTORY_SEPARATOR . 'User' . DIRECTORY_SEPARATOR . 'Admin' . DIRECTORY_SEPARATOR . 'Avatar';
        $this->storageBuildConfig['url'] = $this->storageBuildConfig['url'] . DIRECTORY_SEPARATOR . 'User' . DIRECTORY_SEPARATOR . 'Admin' . DIRECTORY_SEPARATOR . 'Avatar';

        $url = $this->storageSave($file);

        return $this->success($url);
    }

    public function companyInfo(Request $request)
    {
        $file = $request->file('file');

        $this->storageBuildConfig['root'] = $this->storageBuildConfig['root'] . DIRECTORY_SEPARATOR . 'Company';
        $this->storageBuildConfig['url'] = $this->storageBuildConfig['url'] . DIRECTORY_SEPARATOR . 'Company';

        $url = $this->storageSave($file);

        return $this->success($url);
    }

    public function clientUserPetAvatar(Request $request)
    {
        $file = $request->file('file');

        $this->storageBuildConfig['root'] = $this->storageBuildConfig['root'] . DIRECTORY_SEPARATOR . 'ClientUser' . DIRECTORY_SEPARATOR . 'Pet' . DIRECTORY_SEPARATOR . 'Avatar';
        $this->storageBuildConfig['url'] = $this->storageBuildConfig['url'] . DIRECTORY_SEPARATOR . 'ClientUser' . DIRECTORY_SEPARATOR . 'Pet' . DIRECTORY_SEPARATOR . 'Avatar';

        $url = $this->storageSave($file);

        return $this->success($url);
    }
}