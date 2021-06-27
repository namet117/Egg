<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Constants\StatusConst;
use App\Exception\EggException;
use App\Traits\Response;
use Hyperf\Di\Annotation\Inject;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class EggExceptionHandler extends ExceptionHandler
{
    use Response;

    /**
     * @Inject
     *
     * @var \App\Service\Auth
     */
    private $auth;

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $this->stopPropagation();
        $body = json_encode($this->failed($throwable->getMessage(), $throwable->getCode() ?: 1));
        $token = $throwable->getCode() === StatusConst::NO_TOKEN
            ? $this->auth->createToken()
            : $this->auth->getToken();
        $this->auth->saveTokenInfo();
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Egg-Token', $token)
            ->withBody(new SwooleStream($body));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof EggException;
    }
}
