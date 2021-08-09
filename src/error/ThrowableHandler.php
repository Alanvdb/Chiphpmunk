<?php

namespace Chiphpmunk\Error;

use Throwable;

class ThrowableHandler
{
    /**
     * @var Throwable[] $throwables All thrown throwables
     */
    private $throwables = [];

    /**
     * Handles throwable
     *
     * @param Throwable $throwable catched throwable
     * 
     * @return void
     */
    public function handle(Throwable $throwable)
    {
        // $this->loadPrevious($throwable);
        throw $throwable;
    }

    /**
     * Loads previous throwables
     *
     * @param Throwable $throwable throwable to load previous from
     * 
     * @return void
     */
    private function loadPrevious(Throwable $throwable)
    {
        $previous = $throwable->getPrevious();

        if ($previous !== null) {
            $this->throwables[] = $previous;
            $this->loadPrevious($previous);
        }
    }
}