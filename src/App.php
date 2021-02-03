<?php

namespace Chiphpmunk;

use Chiphpmunk\Http\ServerRequestInterface;
use Chiphpmunk\Http\ResponseInterface;
use Chiphpmunk\Middleware\MiddlewareInterface;

class App
{
    /**
     * Constructor
     * 
     * @param
     */
    public function __construct()
    {

    }

    /**
     * Runs application
     * 
     * @param ServerRequestInterface $request Incoming HTTP request
     * 
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request) : ResponseInterface
    {
        $response = new Http\Response();
        $response->getBody()->write('Hello world !');
        return $response;
    }
}
