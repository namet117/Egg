<?php

declare(strict_types=1);

return [
    // 百度AI相关配置
    'baidu_ai' => [
        'app_id' => env('BAIDU_AI_APP_ID', ''),
        'key' => env('BAIDU_AI_KEY', ''),
        'secret' => env('BAIDU_AI_SECRET', ''),
    ],
    // 微信小程序相关配置
    'mp' => [
        'key' => env('MP_APP_KEY', ''),
        'secret' => env('MP_APP_SECRET', ''),
    ],
];
