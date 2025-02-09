<?php

namespace App\Http\Resources;

class ClientUserAddressResource extends CommentsResource
{
    protected function resourceData(): array
    {
        $paginate = $this->additional['paginate'] ?? true;
        $format = $this->additional['format'] ?? 'default';

        return match ($paginate) {
            true => match ($format) {
                'index' => [
                    'id' => $this->id,
                ],
                'show' => [
                    'id' => $this->id,
                ],
                'default' => []
            },
            false => [
                'value' => $this->id,
                'label' => $this->person_name . '【' . $this->full_address . '】(+' . $this->person_phone_prefix . ' ' . $this->person_phone_number . ')'
            ]
        };
    }
}
