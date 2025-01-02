<?php

return [
    /**
     * 'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf('%s%s', 'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1', Laravel\Sanctum\Sanctum::currentApplicationUrlWithPort()))),
     *
     * 'guard' => ['web'],
     *
     * 'expiration' => null,
     *
     * 'token_prefix' => env('SANCTUM_TOKEN_PREFIX', '')),
     *
     * 'middleware' => [
     *     'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
     *     'encrypt_cookies' => Illuminate\Cookie\Middleware\EncryptCookies::class,
     *     'validate_csrf_token' => Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class
     * ]
     *
     */
    'alg' => env('JWT_ALG', 'HS256'),

    'sign_hash' => env('JWT_SIGN_HASH', 'sha256'),

    'secret' => env('JWT_SECRET'),

    /**
     * 过期时间，秒
     */
    'ttl' => env('JWT_TTL', 36000),


    'admin' => [
        'token_prefix' => 'wangxingren_fun_admin_'
    ],
    'wechat' => [
        'token_prefix' => 'wangxingren_fun_wechat_'
    ]
];