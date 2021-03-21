<?php

namespace Chiphpmunk\Middleware;

interface DispatcherInterface
{
    /**
     * @param MiddlewareInterface ...$middlewares Middleware queue
     */
    public function __construct(MiddlewareInterface ...$middlewares);
}
