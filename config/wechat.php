<?php

return [
    // 小程序
    'miniprogram' => [
        'app_id' => env('WECHAT_MINIPROGRAM_APPID'),

        'app_secret' => env('WECHAT_MINIPROGRAM_APP_SECRET'),

        'server' => [
            'code2session' => [
                'uri' => 'sns/jscode2session',
                'method' => 'get'
            ],

            'getPaidUnionid' => [
                'uri' => 'wxa/getpaidunionid',
                'method' => 'get'
            ],

            'getPhoneNumber' => [
                'uri' => 'wxa/business/getuserphonenumber',
                'method' => 'post'
            ],

            'getAccessToken' => [
                'uri' => 'cgi-bin/token',
                'method' => 'get'
            ],

            'getStableAccessToken' => [
                'uri' => 'cgi-bin/stable_token',
                'method' => 'post'
            ]
        ]
    ],

    // 商户平台
    'merchant' => [
        'mchid' => env('WECHAT_PAY_SP_MCHID'),

        'apiclient_serial' => env('WECHAT_PAY_SP_CERT_SERIAL'),

        'api_v3_key' => env('WECHAT_PAY_API_V3_KEY')
    ]
];
