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

    private function selectBase()
    {
        return $this->model->withoutGlobalScopes()->select('date')->distinct()->orderBy('date', 'desc');
    }

    public function getAllDate()
    {
        $query = $this->selectBase();
        return $query->get();
    }

    public function getAllDateWithTimes()
    {
        $query = $this->selectBase()->with(['times']);

        return $query->get();
    }

    public function getPageDate(int $page = 1, $pageSize = 10, bool $simplePaginate = false)
    {
        $query = $this->selectBase();

        return $simplePaginate ? $query->simplePaginate($pageSize, ['date'], 'page', $page) : $query->paginate($pageSize, ['date'], 'page', $page);
    }

    public function getPageDateWithTimes(int $page = 1, $pageSize = 10, bool $simplePaginate = false)
    {
        $query = $this->selectBase()->with(['times']);

        return $simplePaginate ? $query->simplePaginate($pageSize, ['date'], 'page', $page) : $query->paginate($pageSize, ['date'], 'page', $page);
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