<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BusinessException extends Exception
{
    /**
     * 业务异常构造函数
     * @param array $codeResponse 状态码
     * @param $info 自定义返回信息，不为空时会替换掉 codeResponse 里面的 message 文字信息
     */
    public function __construct(array $codeResponse, $info = '')
    {
        [$code, $message] = $codeResponse;
        parent::__construct($info ?: $message, $code);
    }

    /**
     * 报告异常
     */
    public function report(): void
    {

    }

    /**
     * 将异常渲染成 HTTP 响应
     */
//    public function render(Request $request): Response
//    {
//        return true;
//    }
}
