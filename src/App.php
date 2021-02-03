<?php

namespace Chiphpmunk;

use Chiphpmunk\Http\ServerRequestInterface;
use Chiphpmunk\Http\ResponseInterface;

use Chiphpmunk\Middleware\Dispatcher;
use Chiphpmunk\Error\ThrowableHandlerMiddleware;
use Chiphpmunk\Configuration\ConfigurationLoaderMiddleware;
use Chiphpmunk\Routing\RoutingMiddleware;

class App
{
    /**
     * @var string $config Configuration file path
     */
    private $config;

    /**
     * Constructor
     * 
     * @param string $configFile Configuration file
     * If no file is provided, application will use the default configuration
     */
    public function __construct(string $configFile = '')
    {
        $this->config = $configFile;
    }

    /**
     * Runs application
     * 
     * @param ServerRequestInterface $request Incoming HTTP request
     * 
     * @throws RuntimeException If no modules were provided in the configuration file
     * 
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request) : ResponseInterface
    {
        return (new Dispatcher(
            new ThrowableHandlerMiddleware(),
            new ConfigurationLoaderMiddleware(),
            new RoutingMiddleware()
        ))->handle($request->withAttribute('config', $this->config));
    }
}
