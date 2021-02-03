<?php

namespace Chiphpmunk\Middleware;

use Chiphpmunk\Http\ServerRequestHandlerInterface;

interface DispatcherInterface extends ServerRequestHandlerInterface
{
    /**
     * @param MiddlewareInterface ...$middlewares Middleware queue
     */
    public function __construct(MiddlewareInterface ...$middlewares);
}
