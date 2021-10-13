<?php

namespace Chiphpmunk\Routing;

use Chiphpmunk\Http\Uri;

/**
 * The router class must be instanciable without arguments.
 */
interface RouterInterface
{
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
     * @return self
     */
    public function map(string $methods, string $uriPattern, $target, string $name = '') : RouterInterface;

    /**
     * @param string $method HTTP method
     * @param string $uri    URI to to look for
     * 
     * @return Route|null The route that matches specified method and URI
     */
    public function catch(string $method, string $uri) : ?Route;

    /**
     * Builds URI from route with provided name applying specified vars.
     *
     * @param string         $name Route identifier
     * @param string[]|int[] $vars Associative array of URI vars
     * 
     * @throws InvalidArgumentException
     * If specified name is empty or does not exist.
     * If a provided var name does not exist in route.
     *
     * @return Uri|null The generated URI or null if no route was found
     */
    public function buildUri(string $name, array $vars = []) : ?Uri;
}
