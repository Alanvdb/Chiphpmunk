<?php

namespace Chiphpmunk\Routing;

use Chiphpmunk\App\Components;
use Chiphpmunk\Http\ResponseInterface;
use Chiphpmunk\Http\Response;
use Chiphpmunk\Middleware\DispatcherInterface;
use Chiphpmunk\Middleware\MiddlewareInterface;

class RoutingMiddleware extends Router implements MiddlewareInterface
{
    /**
     * Process an HTTP request to produce an HTTP response.
     * If methods is unable to produce the response, it returns the response from $dispatcher
     *
     * @param Components          $components Application components
     * @param DispatcherInterface $dispatcher Middleware dispatcher
     *
     * @return ResponseInterface HTTP response
     */
    public function process(Components $components, DispatcherInterface $dispatcher) : ResponseInterface
    {
        $request = $components->getRequest();
        $route = $components->getRouter()->catch($request->getMethod(), $request->getUri()->getPath());
        $target = $route->getTarget();
        return $target($components);
    }
}