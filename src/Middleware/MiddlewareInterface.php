<?php

namespace Chiphpmunk\Middleware;

use Chiphpmunk\App\Components;
use Chiphpmunk\Http\ResponseInterface;

interface MiddlewareInterface
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
    public function process(
        Components $components,
        DispatcherInterface $dispatcher
    ) : ResponseInterface;
}
