<?php

namespace App\Http\Resources;

use App\Enums\PetCategoryEnum;

class PetBreedResource extends CommentsResource
{

    /**
     * @inheritDoc
     */
    protected function resourceData(): array
    {
        $paginate = $this->additional['paginate'] ?? true;
        $format = $this->additional['format'] ?? 'default';

//        dd($paginate, $format);

        return match ($paginate) {
            true => match ($format) {
                'index' => [
                    'id' => $this->id,
                    'title' => $this->title,
                    'type' => PetCategoryEnum::from($this->type)->name(),
//                    'is_sync_attr' => $this->is_sync_attr
                ],
                'show' => [
                    'id' => $this->id,
                    'title' => $this->title,
                    'type' => strval($this->type),
                    'letter' => $this->letter,
                    'product_trademark_id' => $this->product_trademark_id,
                    'product_category_id' => $this->product_category_id,
//                    'is_sync_attr' => $this->is_sync_attr,
//                    'sync_product_trademark_id' => $this->sync_product_trademark_id,
//                    'sync_product_category_id' => $this->sync_product_category_id,
//                    'sync_product_attr_id' => $this->attrs->pluck('id')[0] ?? null
                ],
                'default' => []
            },
            false => match ($format) {
                'sku_index' => [
                    'id' => $this->id,
                    'title' => $this->title,
                    'sku' => (new BaseCollection($this->sku))->additional(['resource' => 'App\Http\Resources\ProductSkuResource', 'format' => 'index', 'paginate' => false]),
                    'row_span' => count($this->sku)
                ],
                default => [
                    'value' => $this->id,
                    'label' => $this->title
                ]
            }
        };
    }
}