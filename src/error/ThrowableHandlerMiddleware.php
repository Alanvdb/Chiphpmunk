<?php

namespace Chiphpmunk\Error;

use Chiphpmunk\Http\ServerRequestInterface;
use Chiphpmunk\Http\ResponseInterface;
use Chiphpmunk\Middleware\MiddlewareInterface;
use Chiphpmunk\Middleware\DispatcherInterface;

use Throwable;
use RuntimeException;

class ThrowableHandlerMiddleware implements MiddlewareInterface
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
        try {
            $response = $dispatcher->handle($request);
            if (!$response instanceof ResponseInterface) {
                throw new RuntimeException('Middleware dispatcher did not return any response.');
            }
            return $response;
        } catch (Throwable $t) {
            throw new \Exception('Throwable catched !', 0, $t);
        }
    }
}
