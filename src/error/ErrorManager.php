<?php

namespace Chiphpmunk\Error;

class ErrorManager
{
    /**
     * @var bool Whether or not errors are displayed
     */
    private $areDisplayed = false;

    /**
     * Sets whether or not errors will be displayed.
     * 
     * @param bool $areDisplayed Whether or not errors will be displayed
     * 
     * @return bool Whether or not setting has been updated
     */
    public function displayErrors(bool $areDisplayed = true) : bool
    {
        $currentValue = ini_get('display_errors');
        if ($areDisplayed && $currentValue !== '1') {
            ini_set('display_errors', '1');
            $this->areDisplayed = true;
        } elseif (!$areDisplayed && $currentValue !== '0') {
            ini_set('display_errors', '0');
            $this->areDisplayed = false;
        }
    }
}
