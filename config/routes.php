<?php

declare(strict_types=1);

use Hyperf\HttpServer\Router\Router;

Router::post('/status', 'App\Controller\AuthController@status');
Router::post('/initWx', 'App\Controller\AuthController@loginByWxCode');

Router::post('/user/stocks', 'App\Controller\UserController@getUserStocks');
Router::post('/user/updateStock', 'App\Controller\UserController@updateStock');

Router::post('/searchStocks', 'App\Controller\StockController@searchStocks');
