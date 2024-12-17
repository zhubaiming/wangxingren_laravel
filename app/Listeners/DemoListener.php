<?php

namespace App\Listeners;

use App\Events\DemoEvent;
use Illuminate\Support\Facades\Log;

class DemoListener
{
    public function __construct()
    {

    }

    public function handle(DemoEvent $event): void
    {
        Log::channel('test')->debug('我是来测试事件的');
    }
}