<?php

namespace App\Http\Resources;

use App\Enums\GenderEnum;

class ClientUserResource extends CommentsResource
{
    protected function resourceData(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => '+' . $this->phone_prefix . ' ' . $this->phone_number,
            'gender' => GenderEnum::from($this->gender)->name('people'),
            'birthday' => $this->birthday,
            'created_at' => $this->created_at,
            'status_color' => $this->transformStatus($this->is_freeze ? 1 : ($this->deleted_at ? 2 : 0)),
            'status' => $this->is_freeze ? '冻结' : ($this->deleted_at ? '已注销' : '正常'),
        ];
    }

    private function transformStatus($status)
    {
        switch ($status) {
            case 0:
                return ['type' => 'success', 'color' => []];
            case 1:
                return ['type' => 'warning', 'color' => []];
            case 2:
                return ['type' => 'error', 'color' => []];
            default:
                return ['type' => '', 'color' => ['color' => '#EAEAEF']];
        }
    }
}