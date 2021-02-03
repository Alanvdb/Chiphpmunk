<?php

namespace Chiphpmunk\Configuration;

use Chiphpmunk\Http\ServerRequestInterface;
use Chiphpmunk\Http\ResponseInterface;
use Chiphpmunk\Middleware\MiddlewareInterface;
use Chiphpmunk\Middleware\DispatcherInterface;
use Chiphpmunk\Routing\Router;

class ConfigurationLoaderMiddleware implements MiddlewareInterface
{
    /**
     * @const string DEFAULT_CONFIG Default configuration file
     */
    private const DEFAULT_CONFIG = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

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
        $configFile = $request->getAttribute('config');
        if ($configFile === '') {
            if (!is_file(self::DEFAULT_CONFIG)) {
                throw new RuntimeException('The default configuration file is missing: "' . self::DEFAULT_CONFIG . '".');
            }
            $config = require self::DEFAULT_CONFIG;
            if (!is_array($config)) {
                throw new RuntimeException('The default configuration file must return an array.');
            }
        } else {
            if (!is_file($configFile)) {
                throw new RuntimeException('Provided configuration is not a file: "' . $configFile . '".');
            }
            $config = require $configFile;
            if (!is_array($config)) {
                throw new RuntimeException('Provided configuration file must return an array.');
            }
        }
        $request = $request
            ->withAttribute('config', $config)
            ->withAttribute('router', new Router());

        return $dispatcher->handle($request);
    }
}
