<?php

namespace Chiphpmunk\Routing;

use Chiphpmunk\Http\Uri;

use InvalidArgumentException;

class Router implements RouterInterface
{
    // =================================================================================================================
    //
    //      ATTRIBUTES
    //
    // =================================================================================================================

    /**
     * @var Route[] $routes Application routes
     */
    private $routes = [];

    /**
     * @var Route[] $NamedRoutes Named routes
     */
    private $namedRoutes = [];

    // =================================================================================================================
    //
    //      METHODS
    //
    // =================================================================================================================

    /**
     * Adds route with provided parameters and returns it.
     * 
     * @param string $methods    HTTP method used to access route (case sensitive)
     *                           Several method can be provided separated with "|" (like "GET|POST").
     * @param string $uriPattern An URI where each vars are surrounded with "{}" (like "/post-{id}")
     * @param mixed  $target     Route target
     * @param string $name       Route identifier
     * 
     * @throws InvalidArgumentException If $methods argument is an empty string
     * 
     * @return Route The created route
     */
    public function map(string $methods, string $uriPattern, $target, string $name = '') : Route
    {
        $route = new Route($methods, $uriPattern, $target);
        if ($name !== '') {
            $this->namedRoutes[$name] = $route;
        } else {
            $this->routes[] = $route;
        }
        return $route;
    }

    /**
     * @param string $method HTTP method
     * @param string $uri    URI to to look for
     * 
     * @return Route|null The route that matches specified method and URI
     */
    public function catch(string $method, string $uri) : ?Route
    {
        foreach ($this->namedRoutes as $route) {
            if ($route->match($method, $uri)) {
                return $route;
            }
        }
        foreach ($this->routes as $route) {
            if ($route->match($method, $uri)) {
                return $route;
            }
        }
        return null;
    }

    /**
     * Builds URI from route with provided name applying specified vars.
     *
     * @param string         $name Route identifier
     * @param string[]|int[] $vars Associative array of URI vars
     * 
     * @throws InvalidArgumentException
     * If specified name is empty.
     * If a provided var name does not exist in route.
     *
     * @return Uri|null The generated URI or null if no route was found
     */
    public function buildUri(string $name, array $vars = []) : ?Uri
    {
        if ($name === '') {
            throw new InvalidArgumentException('$name argument cannot be empty.');
        }
        if (!isset($this->namedRoutes[$name])) {
            return null;
        }
        return $this->namedRoutes[$name]->buildUri($vars);
    }
}
