<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Broadcaster
    |--------------------------------------------------------------------------
    |
    | This option controls the default broadcaster that will be used by the
    | framework when an event needs to be broadcast. You may set this to
    | any of the connections defined in the "connections" array below.
    |
    | Supported: "reverb", "pusher", "ably", "redis", "log", "null"
    |
    */

//    'default' => env('BROADCAST_CONNECTION', 'null'),
    'default' => 'reverb',

    /*
    |--------------------------------------------------------------------------
    | Broadcast Connections
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the broadcast connections that will be used
    | to broadcast events to other systems or over WebSockets. Samples of
    | each available type of connection are provided inside this array.
    |
    */

    'connections' => [

        'reverb' => [
            'driver' => 'reverb',
//            'key' => env('REVERB_APP_KEY'),
            'key' => 'reverb-key-123456',
//            'secret' => env('REVERB_APP_SECRET'),
            'secret' => 'reverb-secret-123456',
//            'app_id' => env('REVERB_APP_ID'),
            'app_id' => 'reverb-app_id-123456',
            'options' => [
//                'host' => env('REVERB_HOST'),
//                'host' => '127.0.0.1',
//                'host' => '172.18.0.5',
                'host' => 'php_broadcast_chongwu',
//                'port' => env('REVERB_PORT', 443),
                'port' => 9001,
//                'scheme' => env('REVERB_SCHEME', 'https'),
                'scheme' => 'http',
//                'useTLS' => env('REVERB_SCHEME', 'https') === 'https',
                'useTLS' => false,
            ],
            'client_options' => [
                // Guzzle client options: https://docs.guzzlephp.org/en/stable/request-options.html
            ],
        ],

        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'host' => env('PUSHER_HOST') ?: 'api-' . env('PUSHER_APP_CLUSTER', 'mt1') . '.pusher.com',
                'port' => env('PUSHER_PORT', 443),
                'scheme' => env('PUSHER_SCHEME', 'https'),
                'encrypted' => true,
                'useTLS' => env('PUSHER_SCHEME', 'https') === 'https',
            ],
            'client_options' => [
                // Guzzle client options: https://docs.guzzlephp.org/en/stable/request-options.html
            ],
        ],

        'ably' => [
            'driver' => 'ably',
            'key' => env('ABLY_KEY'),
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],

    ],

];
