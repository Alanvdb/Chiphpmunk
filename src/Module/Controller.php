<?php

namespace Chiphpmunk\Module;

use Chiphpmunk\Http\ServerRequestInterface;

abstract class Controller
{
    /**
     * @var ServerRequestInterface $request Incoming HTTP request
     */
    protected $request;
    
    /**
     * Constructor
     * 
     * @param ServerRequestInterface $request Incoming HTTP request
     */
    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }
}
