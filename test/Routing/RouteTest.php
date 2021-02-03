<?php

use PHPUnit\Framework\TestCase;

use Chiphpmunk\Routing\Route;

class RouteTest extends TestCase
{
    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / __construct()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Target specified in constructor must be returned via getTarget() method.
     * Route from an URI pattern without parameters must return an empty array via getParams() method.
     */
    public function test_construct_withValidParameters()
    {
        $route = new Route('GET', '/home', 'test');
        $this->assertSame(
            'test',
            $route->getTarget(),
            'Target specified in constructor must be returned via getTarget() method.'
        );
        $this->assertSame(
            [],
            $route->getParams(),
            'Route from an URI pattern without parameters must return an empty array via getParams() method.'
        );
    }

    /**
     * Constructor must throw InvalidArgumentException if $method argument is empty.
     */
    public function test_construct_withEmptyMethod()
    {
        $this->expectException(InvalidArgumentException::class);
        new Route('', '/home', 'test');
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / match()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * match() must return true if URI pattern provided is the same as the one specified in constructor.
     */
    public function test_match_fromPatternWithoutVars()
    {
        $route = new Route('GET', '/home', 'test');
        $this->assertTrue(
            $route->match('GET', '/home'),
            'match() must return true if URI pattern provided is the same as the one specified in constructor.'
        );
    }

    /**
     * match() must return false if URI pattern provided is not the same as the one specified in constructor.
     */
    public function test_match_fromInvalidPatternWithoutVars()
    {
        $route = new Route('GET', '/home', 'test');
        $this->assertFalse(
            $route->match('GET', '/test'),
            'match() must return true if URI pattern provided is the same as the one specified in constructor.'
        );
    }

    /**
     * match() must return false method provided is not supported by the route.
     */
    public function test_match_withUnsupportedMethod()
    {
        $route = new Route('GET', '/home', 'test');
        $this->assertFalse(
            $route->match('POST', '/home'),
            'match() must return false method provided is not supported by the route.'
        );
    }

    /**
     * match() must return true if method provided is supported by the route instanciated with more than one method.
     */
    public function test_match_withSupportedMethod()
    {
        $route = new Route('GET|POST', '/home', 'test');
        $this->assertTrue(
            $route->match('GET', '/home'),
            'match() must return true if method provided is supported by the route instanciated with more than one method.'
        );
    }

    /**
     * match() must return true if URI pattern with var match pattern provided in constructor.
     * getParams() must return params from URI provided to match() method.
     */
    public function test_match_withVar()
    {
        $route = new Route('GET', '/{id}-my-post-title', 'test');
        $this->assertTrue(
            $route->match('GET', '/50-my-post-title'),
            'match() must return true if URI pattern with var match pattern provided in constructor.'
        );
        $this->assertSame(
            ['id' => '50'],
            $route->getParams(),
            'getParams() must return params from URI provided to match() method.'
        );
    }

    /**
     * match() must return true if URI pattern with more than one var match pattern provided in constructor.
     * getParams() must return params from URI provided to match() method when more than one var is specified.
     * 
     * @depends test_match_withVar
     */
    public function test_match_withMoreThanOneVar()
    {
        $route = new Route('GET', '/{id}/{slug}', 'test');
        $this->assertTrue(
            $route->match('GET', '/50/my-post-title'),
            'match() must return true if URI pattern with var match pattern provided in constructor.'
        );
        $this->assertSame(
            ['id' => '50', 'slug' => 'my-post-title'],
            $route->getParams(),
            'getParams() must return params from URI provided to match() method when more than one var is specified.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / where()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * where() must apply regex to specified var.
     */
    public function test_where_applyRegex()
    {
        $route = (new Route('GET', '/{id}-{slug}', 'test'))
            ->where('slug', '[0-9a-zA-Z-_]+')
            ->where('id', '[0-9]+');
        $this->assertTrue(
            $route->match('GET', '/50-my-post-title'),
            'match() must return true from valid URI pattern when route was builded with where() method.'
        );
        $this->assertSame(
            ['id' => '50', 'slug' => 'my-post-title'],
            $route->getParams(),
            'getParams() must return params from URI provided to match() method when Route was built with where() method.'
        );
        $this->assertFalse(
            $route->match('GET', '/test-my-post-title'),
            'where() method seem to not apply regex correctly.'
        );
        $this->assertFalse(
            $route->match('GET', '/test-my-post-25'),
            'where() method seem to not apply regex correctly.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / buildUri()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * 
     */
    public function test_buildUri_withValidArgument()
    {
        $route = new Route('GET', '/{id}-{slug}', 'test');
        $this->assertSame(
            '/50-my-post-title',
            $route->buildUri(['slug' => 'my-post-title', 'id' => 50])
        );
    }
}
