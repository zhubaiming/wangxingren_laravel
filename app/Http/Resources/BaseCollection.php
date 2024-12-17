<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Pagination\AbstractPaginator;

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

        return match ($this->resource instanceof AbstractPaginator) { // 判断当前资源是否为分页资源
            false => parent::toArray($request),
            true => [
                'content' => $this->collection,
                'total' => $this->resource->total(),
                'pageCount' => $this->resource->lastPage(),
                'nextPageUrl' => $this->resource->nextPageUrl()
            ]
        };
    }

    private function setCollects($resource)
    {
        $collectsName = $resource . '::class';

        $this->collects = eval("return $collectsName;");
    }
}
