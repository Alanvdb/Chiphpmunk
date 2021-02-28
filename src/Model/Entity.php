<?php

namespace Chiphpmunk\Model;

use Chiphpmunk\Functionality\HydratorTrait;

abstract class Entity
{
    use HydratorTrait;

    /**
     * @var mixed id Entity identifier
     */
    private $id;

    /**
     * Constructor
     */
    public function __construct(array $data = [])
    {
        $this->hydrate($data);
    }

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

    /**
     * @return bool whether or not entity has an id
     */
    public function hasId() : bool
    {
        return $this->id !== null;
    }

    /**
     * Magic getter
     *
     * @param string $name Attribute name
     * 
     * @return mixed
     */
    public function __get(string $name)
    {
        $method = 'get' . ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->$method();
        }
    }

    /**
     * Magic setter
     *
     * @param string $name  Attribute name
     * @param mixed  $value Attribute value
     * 
     * @return mixed
     */
    public function __set(string $name, $value)
    {
        $method = 'set' . ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->$method($value);
        }
    }
}
