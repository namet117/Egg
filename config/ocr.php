<?php

$keymap = [
    'baidu' => [
        'app_id' => 'BAIDU_AI_APP_ID',
        'key' => 'BAIDU_AI_KEY',
        'secret' => 'BAIDU_AI_SECRET',
    ]
];
$driver = env('OCR_DRIVER', 'baidu');

return [
    'driver' => $driver,
    'app_id' =>  env($keymap[$driver] ?? ''),
    'key' =>  env($keymap[$driver] ?? ''),
    'secret' =>  env($keymap[$driver] ?? ''),
];
