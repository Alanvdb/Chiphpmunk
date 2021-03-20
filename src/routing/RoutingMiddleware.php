<?php

namespace Chiphpmunk\Routing;

use Chiphpmunk\Http\ServerRequestInterface;
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
     * @param ServerRequestInterface $request    HTTP request
     * @param DispatcherInterface    $dispatcher Middleware dispatcher
     *
     * @return ResponseInterface HTTP response
     */
    public function process(ServerRequestInterface $request, DispatcherInterface $dispatcher) : ResponseInterface
    {
        $router = $request->getAttribute('router');
        $route = $router->catch($request->getMethod(), $request->getUri()->getPath());
        $target = $route->getTarget();
        return $target($request);
    }
}