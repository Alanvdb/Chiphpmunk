<?php

namespace Chiphpmunk\Middleware;

use Chiphpmunk\Http\ServerRequestInterface;
use Chiphpmunk\Http\ResponseInterface;

use InvalidArgumentException;
use RuntimeException;

/**
 * This class implements server request handling using middlewares to produce an HTTP response.
 */
class Dispatcher implements DispatcherInterface
{
    /**
     * @var MiddlewareInterface[] $queue Middlewares queue
     */
    private $queue;

    /**
     * @var int $offset Current position in the queue
     */
    private $offset = -1;

    /**
     * Constructor
     *
     * @param MiddlewareInterface ...$middlewares Middleware queue
     * 
     * @throws InvalidArgumentException If argument provided is empty
     */
    public function __construct(MiddlewareInterface ...$middlewares)
    {
        if (empty($middlewares)) {
            throw new InvalidArgumentException('No middlewares were provided.');
        }
        $this->queue = $middlewares;
    }

    /**
     * Handles a request and produces a response.
     *
     * @param ServerRequestInterface $request Incoming HTTP request
     * 
     * @throws RuntimeException If no response were returned
     *
     * @return ResponseInterface HTTP response
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $this->offset++;
        if (isset($this->queue[$this->offset])) {
            $response = $this->queue[$this->offset]->process($request, $this);
        }
        if (!isset($response) || !($response instanceof ResponseInterface)) {
            throw new RuntimeException("No HTTP response were produced by middlewares.");
        }
        return $response;
    }
}
