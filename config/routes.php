<?php

declare(strict_types=1);

use Hyperf\HttpServer\Router\Router;

// JS code登录
Router::post('/init', 'App\Controller\AuthController@loginByWxCode');

Router::post('/user/stocks', 'App\Controller\UserController@getUserStocks');
