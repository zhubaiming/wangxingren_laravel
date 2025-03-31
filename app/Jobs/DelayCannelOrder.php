<?php

namespace App\Jobs;

use App\Enums\OrderStatusEnum;
use App\Models\ClientUserOrder;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Redis;

class DelayCannelOrder implements ShouldQueue
{
    use Queueable;

    private ClientUserOrder $order;

    /**
     * Create a new job instance.
     */
    public function __construct(int $order_no)
    {
        $this->onConnection('redis');

        $this->onQueue(app()->environment('local') ? 'local.delay_cannel_orders' : 'delay_cannel_orders');

        $this->order = ClientUserOrder::where('order_no', $order_no)->first();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->order->status === OrderStatusEnum::paying->value) {
            $this->order->update(['status' => OrderStatusEnum::cancel->value]);

            Redis::connection('order')->lrem('reservation_date_' . $this->order->reservation_date . '-' . $this->order->reservation_car, 0, json_encode([
                'start' => Carbon::parse($this->order->reservation_time_start)->format('H:i'),
                'end' => Carbon::parse($this->order->reservation_time_end)->format('H:i'),
            ], JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        }
    }
}
