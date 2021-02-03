<?php

namespace Chiphpmunk\Module;

use Chiphpmunk\Routing\RouterInterface;
use Chiphpmunk\View\RendererInterface;

interface ModuleInterface
{
    /**
     * Maps Module routes
     * 
     * @param RouterInterface $router Application router
     * 
     * @return void
     */
    public function mapRoutes(RouterInterface $router) : void;

    /**
     * Applies namespaces to the application renderer
     * 
     * @param RendererInterface $renderer Application view renderer
     * 
     * @return void
     */
    public function mapViews(RendererInterface $renderer) : void;
}
