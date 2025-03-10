<?php

namespace App\Jobs;

use App\Enums\OrderStatusEnum;
use App\Models\ClientUserOrder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

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
        if ($this->order->status === OrderStatusEnum::paying) {
            $this->order->update(['status' => OrderStatusEnum::cancel]);
        }
    }
}
