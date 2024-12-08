<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ServiceTimeService extends CommentsService
{
    public function __construct()
    {
        $this->setModel('App\Models\ServiceTime');

        $this->setTable('sys_service_time');
    }

    public function checkDateHas(string $date)
    {
        return $this->model->where(['date' => $date])->count('id');
    }

    public function createMany(array $data)
    {
        DB::beginTransaction();

        try {
            DB::table($this->table_name)->insert($data);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

//            throw $e;
            return false;
        }

        return true;
    }
}