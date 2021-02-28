<?php

namespace Chiphpmunk\Functionality;

trait HydratorTrait
{
    /**
     * Hydrates object with provided array
     *
     * @param array $data Associative array of attributes
     * 
     * @return void
     */
    public function hydrate(array $data) : void
    {
        foreach($data as $offset => $value) {
            $method = 'set' . ucfirst($offset);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }
}