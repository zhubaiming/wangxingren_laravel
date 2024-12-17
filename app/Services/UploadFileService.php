<?php

namespace App\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

class UploadFileService extends CommentsService
{
    public function uploadImage(\SplFileInfo $file, string $fileName, Filesystem $disk = null)
    {
        if (is_null($disk)) {
            $disk = Storage::disk('public');
        }

        if (!$disk->exists($fileName)) {
            $file->move($disk->getConfig()['root'], $fileName);
        }

        return $disk->url($fileName);
    }

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

    public function spuImage(\SplFileInfo $fileInfo)
    {
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