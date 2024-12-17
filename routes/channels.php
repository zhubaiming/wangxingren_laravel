<?php

use Illuminate\Support\Facades\Broadcast;

/**
 * channel 方法接受两个参数
 * 频道的名字和一个回调函数
 * 返回 true 或 false 来表示用户是否被授权在频道上监听
 */

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int)$user->id === (int)$id;
});

// 在这个例子中，需要验证任何试图在私有 orders.1 频道上监听的用户实际上时订单的创建者
Broadcast::channel('orders.{orderId}', function ($user, $orderId) {
    return $user->id === App\Models\Order::findOrNew($orderId)->user_id;
});

Broadcast::channel('user.{toUserId}', function ($user, $toUserId) {
    return $user->id === $toUserId;
});

Broadcast::channel('notify_message', function () {
    return true;
});