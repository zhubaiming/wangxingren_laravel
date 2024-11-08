<?php

namespace App\Services;

use App\Services\BaseService;
use Illuminate\Support\Facades\Storage;

class UploadFileService extends BaseService
{
    public function petAvatar(\SplFileInfo $fileInfo)
    {
        /**
         * if ($request->hasFile('image')) {
         * $upload_file = $request->file('image');
         * if ($upload_file->isValid()) {
         * $file_name = md5_file($upload_file->path()) . '.' . $upload_file->extension();
         *
         * if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($file_name)) {
         * $upload_file->move(storage_path('app/public'), $file_name);
         * }
         *
         * return response()->json([
         * 'status' => '0',
         * 'message' => 'success',
         * 'data' => [
         * 'path' => \Illuminate\Support\Facades\Storage::disk('public')->url($file_name),
         * ]
         * ]);
         * }
         * }
         *
         * return response()->json([
         * 'status' => '-1',
         * 'message' => 'upload:fail'
         * ]);
         */
        if ($fileInfo->isValid()) {
            $fileName = md5_file($fileInfo->path()) . '.' . $fileInfo->extension();

            if (!Storage::disk('public')->exists($fileName)) {
                $fileInfo->move(config('filesystems.disks.public.root'), $fileName);
            }

            return Storage::disk('public')->url($fileName);
        }

        return null;
    }
}