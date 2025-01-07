<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewPayedOrderEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct()
    {

    }

    /**
     * 返回事件应该广播的频道
     *
     */
    public function broadcastOn()
    {
        // 可以返回单个，如果希望在多个频道上进行广播，返回一个array
        return [
//            new PrivateChannel('new_order_notify_admin'), // 新订单通知后台
//            new PresenceChannel('new_order_notify_admin'), // 新订单通知后台
            new Channel('new_order_notify_admin'), // 新订单通知后台
        ];
    }

    /**
     * 事件的广播名称
     * 设置名称后，laravel-echo监听的名称由原来的当前事件的class名变为 .下面是设置的名称
     * ex:
     * Echo.private('new_order_notify_admin').listen('NewPayedOrderEvent', (e)=>{})
     * 变为
     * Echo.private('new_order_notify_admin').listen('.notify.new_payed.order', (e)=>{})
     */
//    public function broadcastAs(): string
//    {
//        return 'notify.new_payed.order';
//    }

    /**
     * 定义广播发送给客户端的数据
     */
//    public function broadcastWith(): array
//    {
//        return [];
//    }

    /**
     * 广播任务放置的队列的名称
     */
//    public function broadcastQueue(): string
//    {
//        return 'notify_admin_new_order';
//    }
}
