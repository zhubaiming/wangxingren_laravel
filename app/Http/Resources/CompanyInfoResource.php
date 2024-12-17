<?php

namespace App\Http\Resources;

class CompanyInfoResource extends CommentsResource
{

    /**
     * @inheritDoc
     */
    protected function resourceData(): array
    {
        return [
            'trade_time_start' => (new \DateTime($this->trade_time_start))->format('H:i'),
            'trade_time_end' => (new \DateTime($this->trade_time_end))->format('H:i'),
            'images' => $this->images,
            'description' => $this->description,
        ];
    }
}