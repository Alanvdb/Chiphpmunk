<?php

namespace Chiphpmunk\Configuration;

use Chiphpmunk\Http\ServerRequestInterface;
use Chiphpmunk\Http\ResponseInterface;
use Chiphpmunk\Middleware\MiddlewareInterface;
use Chiphpmunk\Middleware\DispatcherInterface;
use Chiphpmunk\Routing\Router;
use Chiphpmunk\Module\ModuleInterface;

use RuntimeException;

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
        $request = $this->loadFile($request);
        $request = $this->loadModules($request);

        return $dispatcher->handle($request);
    }

    /**
     * Loads configuration file
     * 
     * @param ServerRequestInterface Incoming HTTP request
     * 
     * @return ServerRequestInterface Modified HTTP request
     */
    private function loadFile(ServerRequestInterface $request) : ServerRequestInterface
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
        return $request->withAttribute('config', $config);
    }

    /**
     * Loads modules
     * 
     * @param ServerRequestInterface Incoming HTTP request
     * 
     * @return ServerRequestInterface Modified HTTP request
     */
    private function loadModules(ServerRequestInterface $request) : ServerRequestInterface
    {
        $config = $request->getAttribute('config');

        if (!array_key_exists('modules', $config)) {
            throw new RuntimeException('Configuration array does not contain "modules" offset.');
        }
        if (!is_array($config['modules'])) {
            throw new RuntimeException('Configuration "modules" offset must contain an array.');
        }
        $moduleClasses = $config['modules'];
        $config['modules'] = [];

        foreach ($moduleClasses as $moduleClass) {
            if (!is_string($moduleClass)) {
                throw new RuntimeException('Each module array values must be a string typed module class name.');
            }
            if (!class_exists($moduleClass)) {
                throw new RuntimeException('Cannot retrieve specified class: "' . $moduleClass . '".');
            }
            $module = new $moduleClass();
            if (!$module instanceof ModuleInterface) {
                throw new RuntimeException('Module classes must extend "' . Module::class . '" abstract class.');
            }
            $module->mapRoutes($request->getAttribute('router'));
            $module->mapViews($request->getAttribute('renderer'));
            $config['modules'][] = $module;
        }
        return $request->withAttribute('config', $config);
    }
}
