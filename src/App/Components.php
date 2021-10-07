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
    private $request;

    /**
     * @var RouterInterface $router The application router
     */
    private $router;

    /**
     * @var SessionInterface|null $session The current session
     */
    private $session;

    /**
     * @var RendererInterface $renderer The view renderer
     */
    private $renderer;

    /**
     * @var mixed[] $config The application configuration
     */
    private $config = [];

    /**
     * @return ServerRequestInterface The server request
     */
    public function getRequest() : ServerRequestInterface
    {
        return $this->request;
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
        $this->request = $request;
        return $this;
    }

    /**
     * @return RouterInterface The application router
     */
    public function getRouter() : RouterInterface
    {
        return $this->router;
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
        $this->router = $router;
        return $this;
    }

    /**
     * @return SessionInterface|null The current session
     */
    public function getSession() : ?SessionInterface
    {
        return $this->session;
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
        $this->session = $session;
        return $this;
    }

    /**
     * @return RendererInterface The view renderer
     */
    public function getRenderer() : RendererInterface
    {
        return $this->renderer;
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
        $this->renderer = $renderer;
        return $this;
    }

    /**
     * Retrieves configuration value
     * 
     * @param string $offset  Configuration offset name
     * @param mixed  $default value to return if searched offset does not exist (default to null)
     * 
     * @return mixed
     */
    public function getConfig(string $offset, $default = null)
    {
        return $this->hasConfig($offset) ? $this->config[$offset] : $default;
    }

    /**
     * Sets configuration value
     * 
     * @param string $offset Configuration offset name
     * @param mixed  $value  Configuration value
     * 
     * @throws InvalidArgumentException if $offset is an empty string
     * 
     * @return self
     */
    public function setConfig(string $offset, $value) : self
    {
        if ($offset === '') {
            throw new InvalidArgumentException('Provided offset is an empty string.');
        }
        $this->config[$offset] = $value;
        return $this;
    }

    /**
     * Returns whether or not provided configuration offset exists
     * 
     * @param string $offset Configuration offset name
     * 
     * @return bool
     */
    public function hasConfig(string $offset) : bool
    {
        return array_key_exists($offset, $this->config);
    }
}