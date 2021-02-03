<?php

use PHPUnit\Framework\TestCase;

use Chiphpmunk\Routing\Router;
use Chiphpmunk\Routing\Route;

class RouterTest extends TestCase
{
    /**
     * setup
     */
    public function setUp() : void
    {
        $this->router = new Router();
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / map()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * map() must return Route instance.
     */
    public function test_map_returnsRoute()
    {
        $route = $this->router->map('GET', '/home', 'test');
        $this->assertInstanceOf(
            Route::class,
            $route,
            'map() must return Route instance.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / buildUri()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * buildUri() must return Route URI with provided parameter values.
     */
    public function test_buildUri_withValidArgument()
    {
        $this->router->map('GET', '/{id}-{slug}', 'test', 'post')->where('id', '[0-9]+')->where('slug', '[a-z-]+');
        $this->assertSame(
            '/50-my-post-title',
            $this->router->buildUri('post', ['id' => 50, 'slug' => 'my-post-title']),
            'buildUri() must return Route URI with provided parameter values.'
        );
    }
}
