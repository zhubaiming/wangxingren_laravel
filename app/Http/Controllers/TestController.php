<?php

namespace App\Http\Controllers;

use App\Events\DemoEvent;
use App\Events\MessageNotification;
use App\Models\Demo\Message;
use Illuminate\Http\Request;

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

//        broadcast(new MessageNotification($message));
//        event(new MessageNotification(987654321));
        MessageNotification::dispatch($message);

        return response()->json(['message' => '成功']);
    }
}
