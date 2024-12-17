<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class CommentsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = $this->resourceData();

        if ($this->created_at) {
            $data['created_at'] = $this->created_at->format('Y-m-d H:i:s');
        }

        if ($this->updated_at) {
            $data['updated_at'] = $this->updated_at->format('Y-m-d H:i:s');
        }

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
