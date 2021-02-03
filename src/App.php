<?php

namespace Chiphpmunk;

use Chiphpmunk\Http\ServerRequestInterface;
use Chiphpmunk\Http\ResponseInterface;

use Chiphpmunk\Routing\Router;
use Chiphpmunk\View\PhpRenderer;

use Chiphpmunk\Middleware\Dispatcher;
use Chiphpmunk\Error\ThrowableHandlerMiddleware;
use Chiphpmunk\Configuration\ConfigurationLoaderMiddleware;
use Chiphpmunk\Session\PhpSessionMiddleware;
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
            new PhpSessionMiddleware(),
            new RoutingMiddleware()
        ))->handle(
            $request
                ->withAttribute('config', $this->config)
                ->withAttribute('router', new Router())
                ->withAttribute('renderer', new PhpRenderer())
            );
    }
}
