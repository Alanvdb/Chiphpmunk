<?php

namespace Chiphpmunk\Http;

interface ServerRequestHandlerInterface
{
    /**
     * Handles a request to produce a response.
     *
     * @param ServerRequestInterface $request HTTP request
     *
     * @return ResponseInterface HTTP response
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface;
}
