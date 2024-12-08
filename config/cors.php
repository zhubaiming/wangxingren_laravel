<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    // 允许跨域的路径
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    // 允许所有请求方法
    'allowed_methods' => ['*'],

    // 允许所有源
    'allowed_origins' => ['*'],

    // 不使用特定的源模式
    'allowed_origins_patterns' => [],

    // 允许所有请求头
    'allowed_headers' => ['*'],

    // 不暴露特定头部
    'exposed_headers' => [],

    // 不缓存预检请求
    'max_age' => 0,

    // 允许发送凭据（如 cookies）
    'supports_credentials' => true,
];
