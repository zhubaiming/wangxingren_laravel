<?php

namespace App\Http\Resources;

class ProductServiceTimeResource extends CommentsResource
{

    /**
     * @inheritDoc
     */
    protected function resourceData(): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'enable_color' => $this->transformEnableColor($this->enable),
            'enable' => $this->enable
        ];
    }

    private function transformEnableColor($enable)
    {
        if ($enable) {
            return ['type' => 'success', 'color' => []];
        }

        return ['type' => 'error', 'color' => []];
    }
}