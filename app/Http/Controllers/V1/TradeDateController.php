<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\SysTradeDate;
use Illuminate\Http\Request;

class TradeDateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validate = arrHumpToLine($request->input());

        ['startDay' => $startDay, 'endDay' => $endDay] = $this->getStartDay($validate['year'], $validate['month']);

        $payload = SysTradeDate::whereBetween('date', [$startDay, $endDay])->orderBy('date', 'asc')->get();

        $payload = array_column($payload->toArray(), null, 'date');

        return $this->success($payload);
    }

    private function getStartDay($year, $month)
    {
        $firstDay = date('Y-m-d H:i:s', mktime(0, 0, 0, $month, 1, $year));

        return [
            'startDay' => date('Y-m-d', strtotime("{$firstDay} -1 month")),
            'endDay' => date('Y-m-d', strtotime("{$firstDay} +2 month -1 day")),
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        SysTradeDate::updateOrCreate(
            ['date' => $id],
            $request->post()
        );

        return $this->message('success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
