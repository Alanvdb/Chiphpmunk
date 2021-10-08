<?php

namespace Chiphpmunk\Module;

use Chiphpmunk\App\Components;
use Chiphpmunk\Http\ResponseInterface;

abstract class AbstractModule
{
    /**
     * Runs Module
     * 
     * @param string     $action     Module action to run
     * @param Components $components Application components
     * 
     * @return ResponseInterface HTTP response
     */
    abstract protected function run(string $action, Components $components) : ResponseInterface;

    /**
     * Called from the router, calls run() method
     * 
     * @param string $method
     * @param array  $arguments
     * 
     * @return ResponseInterface HTTP response
     */
    public function __call(string $method, array $arguments = []) : ResponseInterface
    {
        return $this->run($method, $arguments[0]);
    }
}