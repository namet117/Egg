<?php

declare(strict_types=1);

namespace App\Controller;

use App\Traits\Response;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use \Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Psr\Container\ContainerInterface;

abstract class AbstractController
{
    use Response;


    /**
     * @Inject
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject
     *
     * @var RequestInterface
     */
    protected $request;

    /**
     * @Inject
     *
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @Inject
     * @var ValidatorFactoryInterface
     */
    protected $validationFactory;

}
