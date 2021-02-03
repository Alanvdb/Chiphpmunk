<?php

namespace Chiphpmunk\Model;

abstract class Entity
{
    /**
     * @var mixed id Entity identifier
     */
    private $id;

    /**
     * @return mixed Entity identifier
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets entity identifier
     * 
     * @param mixed $id Entity identifier
     * 
     * @return self
     */
    public function setId($id) : self
    {
        $this->id = $id;
        return $this;
    }
}
