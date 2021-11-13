<?php

namespace Chiphpmunk;

use Chiphpmunk\Http\ServerRequestInterface;
use Chiphpmunk\Http\ResponseInterface;

use Chiphpmunk\App\Components;
use Chiphpmunk\Routing\Router;
use Chiphpmunk\View\PhpRenderer;

use Chiphpmunk\Middleware\Dispatcher;
use Chiphpmunk\Error\ThrowableHandlerMiddleware;
use Chiphpmunk\Module\ModuleLoaderMiddleware;
use Chiphpmunk\Session\PhpSessionMiddleware;
use Chiphpmunk\Routing\RoutingMiddleware;

use Exception;
use RuntimeException;

class App
{
    /**
     * @const string DEFAULT_CONFIG Default configuration file
     */
    private const DEFAULT_CONFIG = __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'default.php';

    /**
     * Runs application
     * 
     * @param ServerRequestInterface $request    Incoming HTTP request
     * @param string                 $configFile The configuration file
     * 
     * @throws RuntimeException On error with configuration file
     * 
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, string $configFile = '') : ResponseInterface
    {
        if ($configFile === '') {
            if (!is_file(self::DEFAULT_CONFIG)) {
                throw new RuntimeException('Default configuration file is missing: "' . self::DEFAULT_CONFIG . '".');
            }
            $configFile = self::DEFAULT_CONFIG;
        }
        if (!is_file($configFile)) {
            throw new RuntimeException('Cannot retrieve configuration file: "' . $configFile . '".');
        }
        $config = require $configFile;
        if (!is_array($config)) {
            throw new RuntimeException('Configuration file must return an array.');
        }

        $components = (new Components())
            ->setRequest($request)
            ->setRouter(new Router())
            ->setRenderer(new PhpRenderer());

        foreach ($config as $configName => $configValue) {
            $method = 'set' . (ucfirst($configName));
            $components->$method($configValue);
        }

        return (new Dispatcher(
            new ThrowableHandlerMiddleware(),
            new ModuleLoaderMiddleware(),
            new PhpSessionMiddleware(),
            new RoutingMiddleware()
        ))->handle($components);
    }
}
