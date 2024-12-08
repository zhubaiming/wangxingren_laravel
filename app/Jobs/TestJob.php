<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class TestJob implements ShouldQueue
{
    use Queueable;

    public $param;

    /**
     * 创建一个新的作业实例
     */
    public function __construct($param = '')
    {
        $this->param = $param;
    }

    /**
     * 执行作业
     */
    public function handle(): void
    {
        Log::info('Hello, ' . $this->param);
    }
}
