<?php

namespace Chiphpmunk\Routing;

use Chiphpmunk\App\Components;
use Chiphpmunk\Http\ResponseInterface;
use Chiphpmunk\Http\Response;
use Chiphpmunk\http\Uri;
use Chiphpmunk\Middleware\DispatcherInterface;
use Chiphpmunk\Middleware\MiddlewareInterface;

use RuntimeException;

class RoutingMiddleware extends Router implements MiddlewareInterface
{
    /**
     * @const int NOT_FOUND Error code thrown when no route is found
     */
    public const NO_ROUTE = 1;

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
        if ($route === null) {
            throw new RuntimeException('No route found.', self::NO_ROUTE);
        }
        $components->setRequest(
            $request->withQueryParams(
                array_merge(
                    Uri::parseQuery($request->getUri()->getQuery()),
                    $route->getParams()
                )
            )
        );
        $target = $route->getTarget();
        return $target($components);
    }
}