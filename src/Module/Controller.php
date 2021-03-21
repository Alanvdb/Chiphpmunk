<?php

namespace Chiphpmunk\Module;

use Chiphpmunk\App\Components;

abstract class Controller
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
}
