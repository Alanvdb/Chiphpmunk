<?php

namespace Chiphpmunk\View;

/**
 * The renderer class must be instanciable without arguments.
 */
interface RendererInterface
{
    /**
     * Adds view namespace
     * 
     * @param string $namespace Namespace use to access view directory
     * @param string $directory Directory the namespace points to
     * 
     * @throws InvalidArgumentException
     * If namespace is empty or contain "@" character.
     * If provided directory does not exists.
     * 
     * @return self
     */
    public function setNamespace(string $namespace, string $directory) : RendererInterface;

    /**
     * Returns generated content
     * 
     * @param string  $view Template identifier
     * For example, if the template "home.php" is contained in the namespace named "default", specify "home@default".
     * @param mixed[] $vars Associative array of vars (keys must have a valid PHP variable name).
     *
     * @throws InvalidArgumentException On any error with arguments.
     *
     * @return string The generated view
     */
    public function render(string $view, array $vars = []) : string;
}
