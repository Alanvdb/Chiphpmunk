<?php

namespace Chiphpmunk\Routing;

use InvalidArgumentException;

class Route
{
    // =================================================================================================================
    //
    //      ATTRIBUTES
    //
    // =================================================================================================================

    /**
     * @var string[] $methods HTTP methods used to access route
     */
    private $methods;

    /**
     * @var string $pattern Route URI pattern
     */
    private $pattern;

    /**
     * @var mixed $target Route target
     */
    private $target;

    /**
     * @var string[] $vars Vars catched from provided URI
     */
    private $parameters = [];

    /**
     * @var string[] $patterns Vars regex patterns
     */
    private $patterns = [];

    // =================================================================================================================
    //
    //      METHODS
    //
    // =================================================================================================================

    /**
     * Constructor
     *
     * @param string $method     HTTP method used to access route (case sensitive)
     *                           Several method can be provided separated with "|" (like "GET|POST").
     * @param string $uriPattern An URI where each vars are surrounded with "{}" (like "/post-{id}")
     * @param mixed  $target     Route target
     * 
     * @throws InvalidArgumentException If $methods argument is an empty string
     */
    public function __construct(string $methods, string $uriPattern, $target)
    {
        if ($methods === '') {
            throw new InvalidArgumentException('$method argument cannot be empty.');
        }
        $this->methods = explode('|', $methods);
        $this->pattern = $uriPattern;
        $this->target  = $target;

        if (preg_match_all('/{(.+)}/U', $this->pattern, $matches)) {
            $varCount = count($matches[1]);
            for ($i = 0; $i < $varCount; $i++) {
                $this->parameters[$matches[1][$i]] = null;
                $this->patterns[$matches[1][$i]] = '.+';
            }
        }
    }

    /**
     * Sets parameter pattern to the parameter with provided name.
     * 
     * @param string $param URI pattern parameter name
     * @param string $regex Regex pattern (without delimiters)
     * 
     * @throws InvalidArgumentException
     * If no parameter exists with provided name
     * If regex pattern is invalid
     * 
     * @return self
     */
    public function where(string $param, string $regex) : self
    {
        if (!isset($this->patterns[$param])) {
            throw new InvalidArgumentException('Provided parameter does not exist in URI pattern.');
        }
        if ($regex === '') {
            throw new InvalidArgumentException('Provided regex cannot be empty.');
        }
        $this->patterns[$param] = $regex;
        return $this;
    }

    /**
     * @return mixed The route target
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @return string[]|null[] Route parameters associated with their name.
     * If no values were set via match() method all parameters will be set to null.
     */
    public function getParams() : array
    {
        return $this->parameters;
    }

    /**
     * Returns whether or not route matches with specified URI and method.
     *
     * If route matches and parameters exists, parameter values are set from provided URI.
     *
     * @return bool
     */
    public function match(string $method, string $uri) : bool
    {
        if (!in_array($method, $this->methods)) {
            return false;
        }
        $regex = '`^' . $this->pattern . '$`';

        foreach ($this->patterns as $paramName => $paramPattern) {
            $regex = str_replace('{' . $paramName . '}', '(' . $paramPattern . ')', $regex);
        }
        if (preg_match_all($regex, $uri, $matches)) {
            array_shift($matches);
            $paramValues = array_column($matches, 0);
            $i = 0;
            foreach (array_keys($this->patterns) as $paramName) {
                $this->parameters[$paramName] = $paramValues[$i];
                $i++;
            }
            return true;
        }
        return false;
    }

    /**
     * Builds URI from provided vars
     * 
     * @param array $vars An associative array of URI vars
     * 
     * @throws InvalidArgumentException If a provided var name does not exists
     * 
     * @return string
     */
    public function buildUri(array $vars) : string
    {
        $uri = $this->pattern;
        foreach ($vars as $name => $value) {
            if (strpos($uri, '{' . $name . '}') === false) {
                throw new InvalidArgumentException('Uri var "' . $name . '" could not be found.');
            }
            $uri = str_replace('{' . $name . '}', $value, $uri);
        }
        return $uri;
    }
}
