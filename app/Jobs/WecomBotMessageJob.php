<?php

namespace App\Jobs;

use Hongyi\Message\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class WecomBotMessageJob implements ShouldQueue
{
    use Queueable;

    private string $msgtype;
    private string $content;

    /**
     * Create a new job instance.
     */
    public function __construct(string $msgtype, string $content)
    {
        $this->onConnection('redis');

        $this->onQueue(app()->isLocal() ? 'local.delay_cannel_orders' : 'wecom_bot_message');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Message::wecom()->bot([
            'msgtype' => $this->msgtype,
            $this->msgtype => [
                'content' => $this->content
            ]
        ]);
    }
}
