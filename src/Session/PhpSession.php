<?php

namespace Chiphpmunk\Session;

use InvalidArgumentException;

class PhpSession implements SessionInterface
{
    /**
     * Constructor
     * 
     * Starts PHP session
     */
    public function __construct()
    {
        session_start();
    }

    /**
     * Sets session var
     * 
     * @param string $offset Session var offset
     * @param mixed  $value  Value to assign
     * 
     * @throws InvalidArgumentException if $offset is empty
     * 
     * @return self
     */
    public function setVar(string $offset, $value) : SessionInterface
    {
        if ($offset === '') {
            throw new InvalidArgumentException('First argument cannot be empty.');
        }
        $_SESSION[$offset] = $value;
        return $this;
    }

    /**
     * Unsets session var
     * 
     * @param string $offset Session var offset
     * 
     * @return void
     */
    public function unsetVar(string $offset) : void
    {
        if (array_key_exists($offset, $_SESSION)) {
            unset($_SESSION[$offset]);
        }
    }

    /**
     * Retrieve session value from specified offset.
     * 
     * @param string $offset  Offset to retrieve value from
     * @param mixed  $default Value to return if offset does not exists
     * 
     * @return mixed
     */
    public function getVar(string $offset, $default = null)
    {
        return $_SESSION[$offset] ?? $default;
    }

    /**
     * Returns wether or not provided offset exists
     * 
     * @param string $offset Session var offset
     * 
     * @return bool
     */
    public function varExists(string $offset) : bool
    {
        return array_key_exists($offset, $_SESSION);
    }
}
