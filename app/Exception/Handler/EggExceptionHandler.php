<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Exception\EggException;
use App\Traits\Response;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class EggExceptionHandler extends ExceptionHandler
{
    use Response;

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $this->stopPropagation();
        $body = json_encode($this->failed($throwable->getMessage(), $throwable->getCode() ?: 1));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withBody(new SwooleStream($body));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof EggException;
    }
}
