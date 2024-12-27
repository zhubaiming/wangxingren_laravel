<?php

namespace App\Http\Resources;

class CouponResource extends CommentsResource
{
    protected function resourceData(): array
    {
        $paginate = $this->additional['paginate'] ?? true;
        $format = $this->additional['format'] ?? 'default';

        return match ($paginate) {
            true => match ($format) {
                'index' => [
                    'id' => $this->id,
                    'title' => $this->title,
                    'amount' => $this->amount,
                    'min_total' => $this->min_total,
                    'expiration_at' => $this->expiration_at,
                    'updated_by' => $this->updated_by
                ],
                'show' => [
                    'id' => $this->id,
                    'title' => $this->title,
                    'amount' => $this->amount,
                    'min_total' => $this->min_total,
                    'expiration_at' => $this->expiration_at,
                    'related_action' => $this->related_action
                ],
                'default' => []
            },
            false => match ($format) {
                default => [
                    'value' => $this->id,
                    'label' => $this->title
                ]
            }
        };
    }
}