<?php

use PHPUnit\Framework\TestCase;

use Chiphpmunk\Http\Request;
use Chiphpmunk\Http\UriInterface;
use Chiphpmunk\Http\Uri;

class RequestTest extends TestCase
{
    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / __construct()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Request must be instanciable without argument.
     */
    public function test_construct_withoutArgument()
    {
        $request = new Request();
        $this->assertInstanceOf(
            request::class,
            $request,
            'Request must be instanciable without argument.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / getMethod()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * If no method was provided getMethod must return "GET".
     * 
     * @depends test_construct_withoutArgument
     * 
     * @return string The default method
     */
    public function test_getMethod_withoutMethod() : string
    {
        $r = new Request();
        $defaultMethod = 'GET';
        $this->assertSame(
            $defaultMethod,
            $r->getMethod(),
            'If no method was provided getMethod must return "GET".'
        );
        return $defaultMethod;
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / withMethod()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * withMethod() must retain the state of the current instance.
     * 
     * @depends test_getMethod_withoutMethod
     * 
     * @param string $defaultMethod Default request method sended by test_getMethod_withoutMethod()
     */
    public function test_withMethod_state(string $defaultMethod)
    {
        $r = new Request();
        $r->withMethod('POST');
        $this->assertSame(
            $defaultMethod,
            $r->getMethod(),
            'withMethod() must retain the state of the current instance.'
        );
    }

    /**
     * withMethod() must return a static instance.
     * 
     * @depends test_construct_withoutArgument
     */
    public function test_withMethod_returnsStatic()
    {
        $r  = new Request();
        $r2 = $r->withMethod('POST');
        $this->assertNotSame(
            $r,
            $r2,
            'withMethod() must return a new instance.'
        );
    }

    /**
     * withMethod() must apply provided method and return it via getMethod() method.
     * 
     * @depends test_getMethod_withoutMethod
     */
    public function test_withMethod_appliesProvidedMethod()
    {
        $r = (new Request())->withMethod('POST');
        $this->assertSame(
            'POST',
            $r->getMethod(),
            'withMethod() must apply provided method and return it via getMethod() method.'
        );
    }

    /**
     * withMethod() must throw InvalidArgumentException if an empty string is provided.
     * 
     * @depends test_construct_withoutArgument
     */
    public function test_withMethod_withEmptyString()
    {
        $this->expectException(InvalidArgumentException::class);
        (new Request())->withMethod('');
    }

    /**
     * withMethod() must throw InvalidArgumentException if provided string contains white spaces.
     * 
     * @depends test_construct_withoutArgument
     */
    public function test_withMethod_withWhiteSpaces()
    {
        $this->expectException(InvalidArgumentException::class);
        (new Request())->withMethod('PO ST');
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / getUri()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * getUri() must return an UriInterface instance.
     * 
     * @depends test_construct_withoutArgument
     */
    public function test_getUri_returnsUriInterface()
    {
        $r = new Request();
        $this->assertInstanceOf(
            UriInterface::class,
            $r->getUri(),
            'getUri() must return an UriInterface instance.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / withUri()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * withUri() must retain the state of the current instance.
     * 
     * @depends test_construct_withoutArgument
     */
    public function test_withUri_state()
    {
        $expected = new Uri('/test');
        $r = (new Request())->withUri($expected);
        $r->withUri(new Uri('/path'));
        $this->assertSame(
            $expected,
            $r->getUri(),
            'withUri() must retain the state of the current instance.'
        );
    }

    /**
     * withUri() must return a static instance.
     * 
     * @depends test_construct_withoutArgument
     */
    public function test_withUri_returnsStatic()
    {
        $r  = new Request();
        $r2 = $r->withUri(new Uri('/path'));
        $this->assertNotSame(
            $r,
            $r2,
            'withUri() must return a static instance.'
        );
    }

    /**
     * withUri() must apply provided URI and return it via getUri() method.
     * 
     * @depends test_construct_withoutArgument
     */
    public function test_withUri_appliesProvidedUri()
    {
        $uri = new Uri('/target');
        $r = (new Request())->withUri($uri);
        $this->assertSame(
            $uri,
            $r->getUri(),
            'withUri() must apply provided URI and return it via getUri() method.'
        );
    }

    /**
     * By default withUri() must update Host header if provided URI contains host component.
     * 
     * @depends test_construct_withoutArgument
     */
    public function test_withUri_updatesHostHeaderByDefault()
    {
        $r = (new Request())->withUri(new Uri('http://localhost/my/path'));
        $this->assertSame(
            'localhost',
            $r->getHeaderLine('Host'),
            'By default withUri() must update Host header if provided URI contains host component.'
        );
    }

    /**
     * withUri() must update Host header if current request has no header even if $preserveHost argument is set to true.
     * 
     * @depends test_construct_withoutArgument
     */
    public function test_withUri_updateHostHeaderIfCurrentHasNot()
    {
        $r = (new Request())->withUri(new Uri('http://localhost'), true);
        $this->assertSame(
            'localhost',
            $r->getHeaderLine('Host'),
            'withUri() must update Host header if current request has no header even if $preserveHost argument is set to true.'
        );
    }

    /**
     * withUri() must preserve host header if current instance has host header and $preserveHost argument is set to true.
     *
     * @depends test_construct_withoutArgument
     */
    public function test_withUri_preserveHost()
    {
        $r = (new Request())
            ->withHeader('Host', 'localhost')
            ->withUri(new Uri('http://google.com/my/path'), true);
        $this->assertSame(
            'localhost',
            $r->getHeaderLine('Host'),
            'withUri() must preserve host header if current instance has host header and $preserveHost argument is set to true.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / getRequestTarget()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * getRequestTarget() must return "/" if no URI is avaible and no request target has been provided.
     * 
     * @depends test_construct_withoutArgument
     * 
     * @return string The default request target
     */
    public function test_getRequestTarget_withoutTarget() : string
    {
        $r = new Request();
        $defaultTarget = '/';
        $this->assertSame(
            $defaultTarget,
            $r->getRequestTarget(),
            'getRequestTarget() must return "/" if no URI is avaible and no request target has been provided.'
        );
        return $defaultTarget;
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / withRequestTarget()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * withTarget() must retain the state of the current instance.
     * 
     * @depends test_getRequestTarget_withoutTarget
     * 
     * @param string $defaultTarget Default request target returned by test_getRequestTarget_withoutTarget()
     */
    public function test_withRequestTarget_state(string $defaultTarget)
    {
        $request = new Request();
        $request->withRequestTarget('/target');
        $this->assertSame(
            $defaultTarget,
            $request->getRequestTarget(),
            'withTarget() must retain the state of the current instance.'
        );
    }

    /**
     * withRequestTarget() must return a new instance.
     * 
     * @depends test_construct_withoutArgument
     */
    public function test_withRequestTarget_returnsNew()
    {
        $r  = new Request();
        $r2 = $r->withRequestTarget('/test');
        $this->assertNotSame(
            $r,
            $r2,
            'withRequestTarget() must return a new instance.'
        );
    }

    /**
     * withRequestTarget() must apply provided target and return it via getRequestTarget() method.
     * 
     * @depends test_construct_withoutArgument
     */
    public function test_withRequestTarget_applyNewTarget()
    {
        $r = (new Request())->withRequestTarget('/target');
        $this->assertSame(
            '/target',
            $r->getRequestTarget(),
            'withRequestTarget() must apply provided target and return it via getRequestTarget() method.'
        );
    }
}
