<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class BaseCollection extends CommentsCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->setCollects($this->additional['resource']);

        $collects = $this->collects;
        $additionalData = $this->additional;

        $this->collection = $this->collection->map(function ($resource) use ($collects, $additionalData) {
            return (new $collects($resource))->additional($additionalData);
        });

        return $this->resource instanceof AbstractPaginator ? ( // 判断当前资源是否为分页资源
        $this->resource instanceof LengthAwarePaginator ? [ // 判断当前资源是否为正常分页
            'content' => $this->collection,
            'total' => $this->resource->total(),
        ] : ($this->resource instanceof Paginator ? [ // 判断当前资源是否为简单分页
            'content' => $this->collection,
            'hasMore' => $this->resource->hasMorePages()
        ] : parent::toArray($request))
        ) : parent::toArray($request);
    }

    private function setCollects($resource)
    {
        $collectsName = $resource . '::class';

        $this->collects = eval("return $collectsName;");
    }
}
