<?php

declare(strict_types=1);

use Hyperf\HttpServer\Router\Router;

Router::post('/init', 'App\Controller\IndexController@init');
