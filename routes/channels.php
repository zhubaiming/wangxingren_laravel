<?php

use Illuminate\Support\Facades\Broadcast;

/**
 * channel 方法接受两个参数
 * 频道的名字和一个回调函数
 * 返回 true 或 false 来表示用户是否被授权在频道上监听
 *
 * 所有的授权回调函数都会将当前认证的用户作为它们的第一个参数，并将任何额外的通配符参数作为它们的后续参数
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


Broadcast::channel('new_order_notify_admin', function ($user) {
    // 当内部返回结果为 true 时，才会允许用户的 socket 监听此频道
    // 可以定义授权，比如用户拥有新订单接收通知的权限
    return true;
//    return ['id' => 999999, 'name' => 'text_name'];
}, ['guards' => ['admin']]);