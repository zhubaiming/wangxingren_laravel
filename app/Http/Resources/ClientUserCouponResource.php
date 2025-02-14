<?php

namespace App\Http\Resources;

class ClientUserCouponResource extends CommentsResource
{
    protected function resourceData(): array
    {
        $paginate = $this->additional['paginate'] ?? true;
        $format = $this->additional['format'] ?? 'default';

        return match ($paginate) {
            true => match ($format) {
                'index' => [
                    'id' => $this->id,
                    'code' => $this->code,
                    'title' => $this->title,
                    'amount_conv' => applyIntegerToFloatModifier($this->amount),
                    'min_total' => $this->min_total,
                    'min_total_conv' => applyIntegerToFloatModifier($this->min_total),
                    'description' => $this->description,
                    'expiration_at' => $this->expiration_at,
                ],
                'show' => [
                    'id' => $this->id,
                    'title' => $this->title,
                    'permissions' => $this->permissions->pluck('id'),
                    'menus' => $this->menus->pluck('id')
                ],
                'default' => []
            },
            false => [
                'value' => $this->code,
                'label' => '¥' . applyIntegerToFloatModifier($this->amount) . (0 === $this->min_total ? '(无限制)' : '(满' . applyIntegerToFloatModifier($this->min_total) . '可用)'),
                'amount' => $this->amount
            ]
        };
    }
}
