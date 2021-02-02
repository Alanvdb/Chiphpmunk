<?php declare(strict_types=1);

namespace Chiphpmunk;

use InvalidArgumentException;

class Container
{
    /**
     * @var mixed[] $components Container components
     */
    private $components = [];

    /**
     * Sets component to the container.
     * 
     * @param string $identifier Component identifier
     * @param mixed  $value      The component
     * 
     * @throws InvalidArgumentException If $identifier is an empty string
     * 
     * @return self
     */
    public function set(string $identifier, $value) : self
    {
        if ($identifier === '') {
            throw new InvalidArgumentException('Identifier cannot be empty.');
        }
        $this->components[$identifier] = $value;
        return $this;
    }

    /**
     * Unsets component from the container.
     * 
     * @param string $identifier Component identifier
     * 
     * @throws InvalidArgumentException If $identifier does not exists
     * 
     * @return self
     */
    public function unset(string $identifier) : self
    {
        if (!array_key_exists($identifier, $this->components)) {
            throw new InvalidArgumentException('Container component "' . $identifier . '" does not exist.');
        }
        unset($this->components[$identifier]);
        return $this;
    }



    /**
     * Returns component with provided identifier.
     * 
     * @param string $identifier Container component identifier
     * 
     * @throws InvalidArgumentException If specified component does not exist.
     * 
     * @return mixed The container component with provided identifier
     */
    public function get(string $identifier)
    {
        if (!array_key_exists($identifier, $this->components)) {
            throw new InvalidArgumentException('Container component "' . $identifier . '" does not exist.');
        }
        return $this->components[$identifier];
    }

    /**
     * @return mixed[] All container components
     */
    public function getAll() : array
    {
        return $this->components;
    }

    /**
     * Returns whether or not container contains component with provided identifier.
     * 
     * @param string $identifier Container component identifier
     * 
     * @return bool Whether or not container contains component with provided identifier.
     */
    public function has(string $identifier) : bool
    {
        return array_key_exists($identifier, $this->components);
    }
}
