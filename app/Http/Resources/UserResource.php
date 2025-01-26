<?php

namespace App\Http\Resources;

use App\Enums\GenderEnum;

class UserResource extends CommentsResource
{
    protected function resourceData(): array
    {
        $paginate = $this->additional['paginate'] ?? true;
        $format = $this->additional['format'] ?? 'default';

        return match ($paginate) {
            true => match ($format) {
                'info' => [
                    'name' => $this->name,
                    'avatar' => $this->avatar,
                    'is_default_passwd' => $this->is_default_passwd,
                    'role' => $this->role->title,
//                    'routes' => (new BaseCollection($this->role->permissions->groupBy('type')[1]))->additional(['resource' => 'App\Http\Resources\UserPermissionResource'])->pluck('code'),
//                    'buttons' => (new BaseCollection($this->role->permissions->groupBy('type')[2]))->additional(['resource' => 'App\Http\Resources\UserPermissionResource'])->pluck('code')
                    'routes' => (new BaseCollection($this->role->menus))->additional(['resource' => 'App\Http\Resources\UserPermissionResource'])->pluck('code'),
                    'buttons' => (new BaseCollection($this->role->permissions))->additional(['resource' => 'App\Http\Resources\UserPermissionResource'])->pluck('code')
                ],
                'index' => [
                    'id' => $this->id,
                    'role' => $this->role->title,
                    'name' => $this->name,
                    'gender' => GenderEnum::from($this->gender)->name('people'),
                    'phone_number' => $this->phone_number,
                    'account' => $this->account,
                    'status' => $this->status,
                    'status_color' => $this->transformStatusColor($this->status),
                    'updated_by' => $this->updated_by,
                    'created_at' => $this->created_at,
                    'updated_at' => $this->updated_at
                ],
                'show' => [
                    'id' => $this->id,
                    'role_id' => $this->role_id,
                    'name' => $this->name,
                    'gender' => $this->gender,
                    'phone_number' => $this->phone_number,
                    'account' => $this->account,
                    'avatar' => $this->avatar,
                    'status' => $this->status,
                ],
                'default' => []
            },
            false => []
        };
    }

    private function transformStatusColor($status)
    {
        if ($status) {
            return ['type' => 'success', 'color' => []];
        }

        return ['type' => 'error', 'color' => []];
    }
}
