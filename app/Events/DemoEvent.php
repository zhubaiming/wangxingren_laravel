<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DemoEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
}

/**
<?php
 *
 * namespace App\Events;
 *
 * use App\Models\Demo\Message;
 * use Illuminate\Broadcasting\Channel;
 * use Illuminate\Broadcasting\InteractsWithSockets;
 * use Illuminate\Broadcasting\PrivateChannel;
 * use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
 * use Illuminate\Foundation\Events\Dispatchable;
 * use Illuminate\Queue\SerializesModels;
 *
 * class MessageNotification implements ShouldBroadcast
 * {
 * use Dispatchable, InteractsWithSockets, SerializesModels;
 *
 * public $message;
 *
 * public $id;
 *
 * public function __construct($id)
 * {
 * $this->id = $id;
 * //        $this->message = $message;
 * }
 *
 * /**
 * * @inheritDoc
 * */
//* public
//function broadcastOn()
// * {
//    * //        return new PrivateChannel('user.' . $this->message->to);
//    *
//    return new Channel('notify_message');
//    * // TODO: Implement broadcastOn() method.
//    *
//}
// *
// * // 自定义广播的消息名
// * public function broadcastAs()
// * {
//    *
//    return 'anyName';
//    *
//}
// * }
//
// */