<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Constants\StatusConst;
use App\Exception\EggException;
use App\Service\Auth;
use App\Traits\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthCheck implements MiddlewareInterface
{
    use Response;

    /**
     * 不检查Token的path白名单.
     */
    const WHITELIST = [
        '/initWx',
    ];

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var \App\Service\Auth
     */
    private $auth;

    public function __construct(ContainerInterface $container, Auth $auth)
    {
        $this->container = $container;
        $this->auth = $auth;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = $request->getHeaderLine('egg-token') ?: '';
        $ignore = in_array($request->getUri()->getPath(), self::WHITELIST);
        if (!$this->auth->checkToken($token) && !$ignore) {
            throw new EggException(StatusConst::getMessage(StatusConst::NO_TOKEN), StatusConst::NO_TOKEN);
        }
        $this->auth->setToken($token);
        if (!$ignore && !$this->auth->isLogin()) {
            throw new EggException(StatusConst::getMessage(StatusConst::NEED_LOGIN), StatusConst::NEED_LOGIN);
        }
        $with_token = false;
        if (!$this->auth->getToken()) {
            $with_token = true;
            $this->auth->createToken();
        }
        $response = $handler->handle($request);
        $this->auth->saveTokenInfo();
        if ($with_token) {
            $response = $response->withHeader('Egg-Token', $this->auth->getToken())
                ->withHeader('Server', 'Egg-Server');
        }

        return $response;
    }
}
