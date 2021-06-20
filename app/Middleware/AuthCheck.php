<?php

declare(strict_types=1);

namespace App\Middleware;

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
        '/init',
    ];

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var \App\Service\Token
     */
    private $token;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->token = $token;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = $request->getHeaderLine('authorization');
        $is_init = in_array($request->getUri()->getPath(), self::WHITELIST);
        if (!$this->token->checkToken($token) && !$is_init) {
            throw new EggException(StatusConst::getMessage(StatusConst::NEED_LOGIN), StatusConst::NEED_LOGIN);
        }

        $response = $handler->handle($request);
        $this->token->saveTokenInfo();

        return $response;
    }
}
