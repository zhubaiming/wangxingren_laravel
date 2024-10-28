<?php

namespace App\Services;

use App\Models\ShopService;

class ShopServiceService
{
    public function createServiceInfo(array $data, bool $return_model = true)
    {
        $model = ShopService::create($data);

        return $return_model ? $model : !is_null($model);
    }

    public function updateServiceInfo(int $id, array $data)
    {
        return ShopService::where(['id' => $id])->update($data);
    }

    public function updateServiceSaleType(int $id, bool $type)
    {
        return ShopService::where(['id' => $id])->update(['is_saling' => $type]);
    }

    public function deleteSoftService(int|array $ids)
    {
        return ShopService::destroy($ids);
    }
}