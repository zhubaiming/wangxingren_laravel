<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseCollection;
use App\Models\ServiceTime;
use App\Services\ServiceTimeService;
use Illuminate\Http\Request;

class ServiceTimeController extends Controller
{
    public function __construct(ServiceTimeService $service)
    {
        $this->service = $service;
    }

    public function dateList()
    {
        $conditions = [];

        $scopes = ['withoutGlobalScopes']; // 调用 popular 作用域

        $relations = ['times'];

        $fields = ['date'];

        $distinct_fields = ['date'];

        $order_by = ['date' => 'DESC'];

        $payload = $this->service->getList($conditions, scopes: $scopes, relations: $relations, fields: $fields, is_distinct: true, distinct_fields: $distinct_fields, order_by: $order_by, is_without_global_Scopes: true, paginate: true);

        return (new BaseCollection($payload))->additional(['resource' => 'App\Http\Resources\ServiceTimeResource', 'format' => __FUNCTION__]);
    }

    public function checkDate(Request $request)
    {
        $date = date('Y-m-d', substr($request->input('date'), 0, 10));

        $payload = $this->service->checkDateHas($date);

        return $this->success([
            'has' => $payload === 0 ? false : true
        ]);
    }

    public function store(Request $request)
    {
        $date_time = date('Y-m-d H:i:s');

        $date = date('Y-m-d', substr($request->input('date'), 0, 10));

        $insertData = [];
        foreach ($request->input('times_list') as $times) {
            $start_time = date('H:i', substr($times['start_time'], 0, 10));
            $end_time = date('H:i', substr($times['end_time'], 0, 10));

            $insertData[] = ['date' => $date, 'start_time' => $start_time, 'end_time' => $end_time, 'created_at' => $date_time, 'updated_at' => $date_time];
        }

        if ($this->service->createMany($insertData)) {
            return $this->message('保存成功');
        }

        return $this->failed('保存失败');
    }
}