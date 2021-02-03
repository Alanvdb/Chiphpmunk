<?php

namespace Chiphpmunk\Module\Home;

use Chiphpmunk\Http\ServerRequestInterface;
use Chiphpmunk\Http\ResponseInterface;
use Chiphpmunk\Module\ModuleInterface;
use Chiphpmunk\Routing\RouterInterface;
use Chiphpmunk\View\RendererInterface;

use InvalidArgumentException;

class HomeModule implements ModuleInterface
{
    /**
     * Maps Module routes
     * 
     * @param Router $router Application router
     */
    public function mapRoutes(RouterInterface $router) : void
    {
        $router->map('GET', '/', [$this, 'index'], 'home.index');
    }

    /**
     * Applies namespaces to the application renderer
     * 
     * @param RendererInterface $renderer Application view renderer
     * 
     * @return void
     */
    public function mapViews(RendererInterface $renderer) : void
    {
        $renderer->setNamespace('home', __DIR__ . DIRECTORY_SEPARATOR . 'views');
    }

    /**
     * __call() magic method
     */
    public function __call(string $method, array $arguments) : ResponseInterface
    {
        if (!($arguments[0] instanceof ServerRequestInterface)) {
            throw new InvalidArgumentException('Argument must implements ServerRequestInterface.');
        }
        return (new HomeController($arguments[0]))->$method();
    }
}
