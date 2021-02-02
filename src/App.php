<?php

namespace Chiphpmunk;

use Chiphpmunk\Http\ServerRequestInterface;
use Chiphpmunk\Http\ResponseInterface;

class App
{
    /**
     * Runs application
     * 
     * @param ServerRequestInterface $request Incoming HTTP request
     * 
     * @return void
     */
    public function process(ServerRequestInterface $request) : void
    {
        $response = new Http\Response();
        $response->getBody()->write('Hello world !');
        $response->send();
    }
}
