<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CommentsCollection extends ResourceCollection
{
    // 去掉分页的 `links` 和 `meta`
    public function withResponse($request, $response)
    {
        $response->setData((object)[
            'code' => 200,
            'message' => '操作成功',
            'payload' => $this->toArray($request),
        ]);
    }
}
