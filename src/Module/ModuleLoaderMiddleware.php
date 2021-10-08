<?php

namespace Chiphpmunk\Module;

use Chiphpmunk\App\Components;
use Chiphpmunk\Http\ResponseInterface;
use Chiphpmunk\Middleware\MiddlewareInterface;
use Chiphpmunk\Middleware\DispatcherInterface;
use Chiphpmunk\Routing\Router;
use Chiphpmunk\Module\AbstractModule;

use RuntimeException;

class ModuleLoaderMiddleware implements MiddlewareInterface
{
    /**
     * Process an HTTP request to produce an HTTP response.
     * If methods is unable to produce the response, it returns the response from $dispatcher
     *
     * @param Components          $components HTTP request
     * @param DispatcherInterface $dispatcher Middleware dispatcher
     * 
     * @throws RuntimeException On error with "modules" configuration offset
     *
     * @return ResponseInterface HTTP response
     */
    public function process(Components $components, DispatcherInterface $dispatcher) : ResponseInterface
    {
        if (!$components->hasConfig('modules')) {
            throw new RuntimeException('Configuration does not contain "modules" offset.');
        }
        $moduleClasses = $components->getConfig('modules');
        if (!is_array($moduleClasses)) {
            throw new RuntimeException('Configuration offset "modules" must contain an array. ' . gettype($moduleClasses) . ' provided.');
        }
        $modules = [];
        foreach ($moduleClasses as $moduleClass) {
            if (!is_string($moduleClass)) {
                throw new RuntimeException('Confuguration "modules" array must only contain string values. ' . gettype($moduleClass) . ' provided.');
            }
            if (!class_exists($moduleClass)) {
                throw new RuntimeException('Cannot retrieve "' . $moduleClass . '" class.');
            }
            $module = new $moduleClass();
            if (!$module instanceof AbstractModule) {
                throw new RuntimeException('Module class "' . $moduleClass . '" must implement "' . ModuleInterface::class . '" interface.');
            }
            if (method_exists($module, 'setup'))
            {
                $module->setup($components);
            }
            $modules[] = $module;
        }
        return $dispatcher->handle($components);
    }
}
