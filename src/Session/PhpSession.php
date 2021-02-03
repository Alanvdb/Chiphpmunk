<?php

namespace Chiphpmunk\Session;

use InvalidArgumentException;

class PhpSession
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
     * @return void
     */
    public function setVar(string $offset, $value) : void
    {
        if ($offset === '') {
            throw new InvalidArgumentException('First argument cannot be empty.');
        }
        $_SESSION[$offset] = $value;
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
