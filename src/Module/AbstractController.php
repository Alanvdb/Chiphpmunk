<?php

namespace Chiphpmunk\Module;

use Chiphpmunk\App\Components;

abstract class AbstractController
{
    /**
     * @var Components $components Application components
     */
    protected $components;
    
    /**
     * Constructor
     * 
     * @param Components $components Application components
     */
    public function __construct(Components $components)
    {
        $this->components = $components;
    }

    /**
     * Returns renderer generated content
     * 
     * @param string  $view Template identifier
     * For example, if the template "home.php" is contained in the namespace named "default", specify "home@default".
     * @param mixed[] $vars Associative array of vars (keys must have a valid PHP variable name).
     *
     * @throws InvalidArgumentException On any error with arguments.
     *
     * @return string The generated view
     */
    protected function render(string $view, array $vars = []) : string
    {
        return $this->components->getRenderer()->render($view, $vars);
    }
}
