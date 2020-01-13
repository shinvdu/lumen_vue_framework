<?php

/*
 * 基本获取后台api配置
 */

return [
    'api_config' => [
        'client' => 'Guzzle',
        'base_uri' => env('API_URI'), // 基本api路径.
        'timeout' => env('API_TIMEOUT') // 超时，单位秒.
    ],
    'wechat_config' => [
        'debug'  => FALSE,
        'app_id' => env('WX_APP_ID'),
        'secret' => env('WX_SECRET'),
        'oauth' => [
            'callback' => '/oauth/callback',
        ],
        'token'  => '',
        // 'aes_key' => null, // 安全模式可选
        'log' => [
            'level' => 'debug',
            // 'file'  => '/tmp/easywechat.log', // XXX: 绝对路径！！！！
        ],
    ],
    'cache_config' => [
        'client' => env('CACHE_UTIL', 'Mysql')
    ],
    'oauth' => [
        'token_url' => env('API_OAUTH_URL'),
        'credential' => [
            'grant_type' => 'client_credentials',
            'client_id' => env('API_CLIENT_ID'),
            'client_secret' => env('API_SECRET'),
            //  'client_secret' => 'wechat_front',
            'scope' => 'api_scope',
        ]
    ],
    'session' => [
        'host'         => env('REDIS_HOST', '127.0.0.1'), // redis主机
        'port'         => env('REDIS_PORT', '6379'), // redis端口
        'password'     => '', // 密码
        'select'       => 0, // 操作库
        'expire'       => 3600, // 有效期(秒)
        'timeout'      => 0, // 超时时间(秒)
        'persistent'   => true, // 是否长连接
        'session_name' => 'acuvue_', // sessionkey前缀
    ],

];

