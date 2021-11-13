<?php

namespace Chiphpmunk\App;

use Chiphpmunk\Http\ServerRequestInterface;
use Chiphpmunk\Routing\RouterInterface;
use Chiphpmunk\Session\SessionInterface;
use Chiphpmunk\View\RendererInterface;

use InvalidArgumentException;

class Components
{
    /**
     * @var ServerRequestInterface $request The server request
     */
    private $items = [];

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Retrieves whether or not provided component name exists
     * 
     * @param string $componentName The component name
     * 
     * @return bool Whether or not component exists
     */
    public function exists(string $componentName) : bool
    {
        return isset($this->items[strtolower($componentName)]);
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > GETTERS & SETTERS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * __call() magic method
     * 
     * @param string $method Method name called
     * @param mixed[] $arguments Arguments passed to the called method
     * 
     * @return mixed
     */
    public function __call(string $method, array $arguments)
    {
        if (preg_match('`^get`', $method)) {
            $componentName = strtolower(substr($method, 3));
            return isset($this->items[$componentName]) ? $this->items[$componentName] : null;
        }
        if (preg_match('`^set`', $method)) {
            $componentName = strtolower(substr($method, 3));
            $this->items[$componentName] = $arguments[0];
            return $this;
        }
    }

    /**
     * Sets the server request
     * 
     * @param ServerRequestInterface $request The server request
     * 
     * @return self
     */
    public function setRequest(ServerRequestInterface $request) : self
    {
        $this->items['request'] = $request;
        return $this;
    }

    /**
     * Sets the application router
     * 
     * @param RouterInterface $router The application router
     * 
     * @return self
     */
    public function setRouter(RouterInterface $router) : self
    {
        $this->items['router'] = $router;
        return $this;
    }

    /**
     * Sets the current session object
     * 
     * @param SessionInterface $session The current session object
     * 
     * @return self
     */
    public function setSession(SessionInterface $session) : self
    {
        $this->items['session'] = $session;
        return $this;
    }

    /**
     * Sets the view renderer
     * 
     * @param RendererInterface $renderer The view renderer
     * 
     * @return self
     */
    public function setRenderer(RendererInterface $renderer) : self
    {
        $this->items['renderer'] = $renderer;
        return $this;
    }
}