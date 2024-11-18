<?php

namespace App\Http\Resources\Wechat;

use App\Http\Resources\CommentsResource;

class UserPetResource extends CommentsResource
{
    protected function resourceData(): array
    {
        $result = [];

        foreach ($this->getAttributes() as $key => $value) {
            if (!in_array($key, ['created_at', 'updated_at', 'deleted_at'])) {
                $result[$key] = $value;
            }
        }

        $result['is_default'] = $this->is_default;
        $result['weight'] = $this->weight;
        $result['gender_conv'] = $this->gender_conv;
        $result['breed_type_conv'] = $this->breed_type_conv;

        return $result;
    }
}
