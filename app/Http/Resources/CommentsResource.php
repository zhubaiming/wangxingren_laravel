<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class CommentsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = $this->resourceData();

        return arrLineToHump($data);
    }

    public function withResponse($request, $response)
    {
        // 确保响应中不转义中文字符
        $response->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    /**
     * 资源需要转化的数组格式
     *
     * @return array
     */
    abstract protected function resourceData(): array;
}
