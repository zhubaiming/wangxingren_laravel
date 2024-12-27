<?php

namespace App\Http\Controllers;

use App\Events\DemoEvent;
use App\Events\NewPayedOrderEvent;
use App\Models\Demo\Message;
use Illuminate\Support\Facades\Broadcast;

class TestController extends Controller
{
    // 测试事件
    public function testEvent()
    {
        DemoEvent::dispatch();

        return response()->json(['message' => '成功']);
    }

    public function send()
    {
        $message = new Message();

        $message->setAttribute('from', 1);
        $message->setAttribute('to', 2);
        $message->setAttribute('message', 'Demo message from user 1 to user 2');

        $message->save();

//        Broadcast::channel('1', '', ['guards' => ['admin']]);

//        broadcast(new MessageNotification($message));
//        event(new MessageNotification(987654321));
//        MessageNotification::dispatch($message);

//        broadcast(new NewPayedOrderEvent());
        NewPayedOrderEvent::dispatch();

        return response()->json(['message' => '成功']);
    }
}
