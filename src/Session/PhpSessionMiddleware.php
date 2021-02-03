<?php

namespace Chiphpmunk\Session;

use Chiphpmunk\Http\ServerRequestInterface;
use Chiphpmunk\Http\ResponseInterface;
use Chiphpmunk\Middleware\MiddlewareInterface;
use Chiphpmunk\Middleware\DispatcherInterface;

class PhpSessionMiddleware extends PhpSession implements MiddlewareInterface
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
        $request->withAttribute('session', $this);
        return $dispatcher->handle($request);
    }
}
