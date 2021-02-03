<?php

use PHPUnit\Framework\TestCase;

use Chiphpmunk\Http\Uri;

class UriTest extends TestCase
{
    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / __construct()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * __construct() method must accept URI string.
     */
    public function test_construct_withUriString()
    {
        $uri = new Uri('http://localhost');
        $this->assertInstanceOf(
            Uri::class,
            $uri,
            '__construct() method must accept URI string.'
        );
    }

    /**
     * Uri class must be instanciable without arguments.
     */
    public function test_construct_withoutArgument()
    {
        $uri = new Uri();
        $this->assertInstanceOf(
            Uri::class,
            $uri,
            'Uri class must be instanciable without arguments.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / __toString()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * __toString() must return an URI prefixed with "//" if an authority is present.
     * 
     * @depends test_construct_withoutArgument
     * @depends test_withHost_applyNewHost
     */
    public function test_toString_withHost()
    {
        $uri = (new Uri())->withHost('localhost');
        $this->assertSame(
            '//localhost',
            (string) $uri,
            '__toString() must return an URI prefixed with "//" if an authority is present.'
        );
    }


    /**
     * __toString() must return userInfo part correctly.
     * 
     * @depends test_construct_withoutArgument
     * @depends test_withUserInfo_applyUserAndPass
     * @depends test_toString_withHost
     */
    public function test_toString_withUserInfo()
    {
        $uri = (new Uri())
            ->withHost('localhost')
            ->withUserInfo('user', 'pass');
        $this->assertSame(
            '//user:pass@localhost',
            (string) $uri,
            '__toString() must return userInfo part correctly.'
        );
    }

    /**
     * __toString() must return port correctly.
     * 
     * @depends test_construct_withoutArgument
     * @depends test_toString_withHost
     * @depends test_withPort_applyNewPort
     */
    public function test_toString_withPort()
    {
        $uri = (new Uri())
            ->withHost('localhost')
            ->withPort(3000);

        $this->assertSame(
            '//localhost:3000',
            (string) $uri,
            '__toString() must return port correctly.'
        );
    }

    /**
     * __toString() must return http scheme correctly.
     * 
     * @depends test_construct_withUriString
     * @depends test_toString_withHost
     * @depends test_withScheme_applyNewScheme
     */
    public function test_toString_withHttpScheme()
    {
        $uri = (new Uri())
            ->withHost('localhost')
            ->withScheme('http');
        $this->assertSame(
            'http://localhost',
            (string) $uri,
            '__toString() must return http scheme correctly.'
        );
    }

    /**
     * __toString() must return https scheme correctly.
     * 
     * @depends test_construct_withUriString
     * @depends test_toString_withHost
     * @depends test_withScheme_applyNewScheme
     */
    public function test_toString_withHttpsScheme()
    {
        $uri = (new Uri())
            ->withHost('localhost')
            ->withScheme('https');
        $this->assertSame(
            'https://localhost',
            (string) $uri,
            '__toString() must return https scheme correctly.'
        );
    }

    /**
     * __toString() must return path correctly.
     * 
     * @depends test_construct_withUriString
     * @depends test_toString_withHost
     * @depends test_withPath_applyNewPath
     */
    public function test_toString_withPath()
    {
        $uri = (new Uri())
            ->withHost('localhost')
            ->withPath('/my/path');
        $this->assertSame(
            '//localhost/my/path',
            (string) $uri,
            '__toString() must return path correctly.'
        );
    }

    /**
     * __toString() must return query correctly.
     * 
     * @depends test_construct_withUriString
     * @depends test_toString_withHost
     * @depends test_withQuery_applyNewQuery
     * @depends test_toString_withPath
     */
    public function test_toString_withQuery()
    {
        $uri = (new Uri())
            ->withHost('localhost')
            ->withPath('/')
            ->withQuery('my=query&param=value');
        $this->assertSame(
            '//localhost/?my=query&param=value',
            (string) $uri,
            '__toString() must return query correctly.'
        );
    }

    /**
     * __toString() must return fragment correctly.
     * 
     * @depends test_construct_withUriString
     * @depends test_toString_withHost
     * @depends test_withFragment_applyNewFragment
     * @depends test_toString_withPath
     */
    public function test_toString_withFragment()
    {
        $uri = (new Uri())
            ->withHost('localhost')
            ->withPath('/')
            ->withFragment('frag');
        $this->assertSame(
            '//localhost/#frag',
            (string) $uri,
            '__toString() must return fragment correctly.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / isRelativeReference()
    // -----------------------------------------------------------------------------------------------------------------

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / getScheme()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * If no scheme is specified getScheme() must return an empty string.
     * 
     * @depends test_construct_withoutArgument
     */
    public function test_getScheme_withoutScheme()
    {
        $uri = new Uri();
        $this->assertSame(
            '',
            $uri->getScheme(),
            'If no scheme is specified getScheme() must return an empty string.'
        );
    }

    /**
     * The trailing ":" is not part of the scheme and must not be returned via getScheme().
     * 
     * @depends test_construct_withUriString
     */
    public function test_getScheme_returnSchemeOnly()
    {
        $uri = new Uri('http://localhost');
        $this->assertStringNotContainsString(
            ':',
            $uri->getScheme(),
            'The trailing ":" is not part of the scheme and must not be returned via getScheme().'
        );
    }

    /**
     * If "http" scheme is in URI getScheme() must return "http".
     * 
     * @depends test_construct_withUriString
     * @depends test_getScheme_returnSchemeOnly
     */
    public function test_getScheme_withHttpScheme()
    {
        $uri = new Uri('http://localhost/');
        $this->assertSame(
            'http',
            $uri->getScheme(),
            'If "http" scheme is in URI getScheme() must return "http".'
        );
    }

    /**
     * If "https" scheme is in URI getScheme() must return "https".
     * 
     * @depends test_construct_withUriString
     * @depends test_getScheme_returnSchemeOnly
     */
    public function test_getScheme_withHttpsScheme()
    {
        $uri = new Uri('https://example.com/');
        $this->assertSame(
            'https',
            $uri->getScheme(),
            'If "https" scheme is in URI getScheme() must return "https".'
        );
    }

    /**
     * Scheme returned by getScheme() method must be normalized to lowercase.
     * 
     * @depends test_construct_withUriString
     * @depends test_getScheme_returnSchemeOnly
     * @depends test_getScheme_withHttpScheme
     */
    public function test_getScheme_isNormalizedToLowercase()
    {
        $uri = new Uri('HtTp://test.net');
        $this->assertSame(
            'http',
            $uri->getScheme(),
            'Scheme returned by getScheme() method must be normalized to lowercase.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / withScheme()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * withScheme() must return retain the state of the previous instance.
     * 
     * @depends test_construct_withUriString
     * @depends test_getScheme_withHttpScheme
     */
    public function test_withScheme_state()
    {
        $uri = new Uri('http://localhost');
        $uri2 = $uri->withScheme('https');
        $this->assertSame(
            'http',
            $uri->getScheme(),
            'withScheme() must return retain the state of the previous instance.'
        );
    }

    /**
     * withScheme() must return a new instance.
     * 
     * @depends test_construct_withUriString
     */
    public function test_withScheme_returnNew()
    {
        $uri = new Uri('http://localhost');
        $uri2 = $uri->withScheme('https');
        $this->assertNotSame(
            $uri,
            $uri2,
            'withScheme() must return a new instance.'
        );
    }

    /**
     * withScheme() must apply provided scheme and return it via getScheme() method.
     * 
     * @depends test_construct_withUriString
     * @depends test_getScheme_withHttpScheme
     */
    public function test_withScheme_applyNewScheme()
    {
        $uri = (new Uri('//localhost'))->withScheme('http');
        $this->assertSame(
            'http',
            $uri->getScheme(),
            'withScheme() must apply provided scheme and return it via getScheme() method.'
        );
    }

    /**
     * withSheme() method must support http scheme case-insensitively.
     * 
     * @depends test_construct_withoutArgument
     * @depends test_withScheme_applyNewScheme
     */
    public function test_withScheme_supportHttpCaseInsensitively()
    {
        $uri = (new Uri())->withScheme('hTTP');
        $this->assertSame(
            'http',
            $uri->getScheme(),
            'withSheme() method must support http scheme case-insensitively.'
        );
    }

    /**
     * withSheme() method must support https scheme case-insensitively.
     * 
     * @depends test_construct_withoutArgument
     * @depends test_getScheme_withHttpScheme
     */
    public function test_withScheme_supportHttpsCaseInsensitively()
    {
        $uri = (new Uri())->withScheme('hTTPs');
        $this->assertSame(
            'https',
            $uri->getScheme(),
            'withSheme() method must support https scheme case-insensitively.'
        );
    }

    /**
     * Provide an empty string to withScheme() method must be equivalent to removing the sheme.
     * 
     * @depends test_construct_withUriString
     * @depends test_getScheme_withoutScheme
     */
    public function test_withScheme_emptyStringArgument()
    {
        $uri = (new Uri('http://localhost'))->withScheme('');
        $this->assertSame(
            '',
            $uri->getScheme(),
            'Provide an empty string to withScheme() method must be equivalent to removing the sheme.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / getAuthority()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * If no authority is present getAuthority() must return an empty string.
     * 
     * @depends test_construct_withoutArgument
     */
    public function test_getAuthority_withoutAuthority()
    {
        $uri = new Uri();
        $this->assertSame(
            '',
            $uri->getAuthority(),
            'If no authority is present getAuthority() must return an empty string.'
        );
    }

    /**
     * If host is present getAuthority() must contain that host.
     * Test with "localhost"
     * 
     * @depends test_construct_withUriString
     */
    public function test_getAuthority_withLocalhost()
    {
        $uri = new Uri('//localhost/my/test');
        $this->assertStringContainsString(
            'localhost',
            $uri->getAuthority(),
            'If localhost is present as host in uri, getAuthority() must return that host.'
        );
    }

    /**
     * If host is present getAuthority() must contain that host.
     * Test with "example.net"
     * 
     * @depends test_construct_withUriString
     */
    public function test_getAuthority_withExampleDotNet()
    {
        $uri = new Uri('//example.net/my/test');
        $this->assertStringContainsString(
            'example.net',
            $uri->getAuthority(),
            'If "example.net" is present as host in uri, getAuthority() must return that host.'
        );
    }

    /**
     * If a port is specified (not the default port) getAuthority() must contain that port
     * separated from the host with ":".
     * 
     * @depends test_construct_withUriString
     */
    public function test_getAuthority_withPort()
    {
        $uri = new Uri('http://localhost:8000/my/test');
        $this->assertSame(
            'localhost:8000',
            $uri->getAuthority(),
            'If a port is specified (not the default port) getAuthority() must contain that port separated from the host with ":".'
        );
    }

    /**
     * getAuthority() must not include the port number if no port is specified.
     * 
     * @depends test_construct_withUriString
     */
    public function test_getAuthority_withoutPort()
    {
        $uri = new Uri('http://example.com');
        $this->assertSame(
            'example.com', 
            $uri->getAuthority(),
            'getAuthority() must not include the port number if no port is specified.'
        );
    }

    /**
     * getAuthority() must not include the port number if its the default port
     * Test with http scheme (default port: 80)
     * 
     * @depends test_construct_withUriString
     */
    public function test_getAuthority_withDefaultHttpPort()
    {
        $uri = new Uri('http://example.com:80');
        $this->assertStringEndsNotWith(
            '80',
            $uri->getAuthority(),
            'getAuthority() must not return the default port (80) for the http scheme.'
        );
    }

    /**
     * getAuthority() must not include the port number if its the default port
     * Test with https scheme (default port: 443)
     * 
     * @depends test_construct_withUriString
     */
    public function test_getAuthority_withDefaultHttpsPort()
    {
        $uri = new Uri('https://example.com:443');
        $this->assertStringEndsNotWith(
            '443',
            $uri->getAuthority(),
            'getAuthority() must not return the default port (443) for the https scheme.'
        );
    }

    /**
     * getAuthority() must return username of userInfo part separated with "@".
     * 
     * @depends test_construct_withUriString
     */
    public function test_getAuthority_withUsername()
    {
        $uri = new Uri('//alanvdb@my-domain.com');
        $this->assertSame(
            'alanvdb@my-domain.com',
            $uri->getAuthority(),
            'getAuthority() must return username of userInfo part separated with "@".'
        );
    }

    /**
     * getAuthority() must return complete userInfo part "username:password@domain.com".
     * 
     * @depends test_construct_withUriString
     * @depends test_getAuthority_withUsername
     */
    public function test_getAuthority_withCompleteUserInfo()
    {
        $uri = new Uri('//username:password@domain.com');
        $this->assertSame(
            'username:password@domain.com',
            $uri->getAuthority(),
            'getAuthority() must return complete userInfo part "username:password@domain.com".'
        );
    }

    /**
     * getAuthority() must return user info and port (not the default port) if specified.
     * 
     * @depends test_construct_withUriString
     * @depends test_getAuthority_withPort
     * @depends test_getAuthority_withCompleteUserInfo
     */
    public function test_getAuthority_withUserInfoAndPort()
    {
        $uri = new Uri('http://username:pass@localhost:8000/my/path');
        $this->assertSame(
            'username:pass@localhost:8000',
            $uri->getAuthority(),
            'getAuthority() must return user info and port (not the default port) if specified.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / getUserInfo()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * If no user info is present getUserInfo must return an empty string.
     */
    public function test_getUserInfo_withoutInfo()
    {
        $uri = new Uri();
        $this->assertSame(
            '',
            $uri->getUserInfo(),
            'If no user info is present getUserInfo must return an empty string.'
        );
    }

    /**
     * The trailing "@" is not part of the user info. getUserInfo() must not return it.
     * 
     * @depends test_construct_withUriString
     */
    public function test_getUserInfo_doNotReturnTrailingA()
    {
        $uri = new Uri('http://username@host.com/');
        $this->assertStringNotContainsString(
            '@',
            $uri->getUserInfo(),
            'The trailing "@" is not part of the user info. getUserInfo() must not return it.'
        );
    }

    /**
     * getUserInfo() must return username if specified.
     * 
     * @depends test_construct_withUriString
     * @depends test_getUserInfo_doNotReturnTrailingA
     */
    public function test_getUserInfo_withUsername()
    {
        $uri = new Uri('http://username@localhost/my/path');
        $this->assertSame(
            'username',
            $uri->getUserInfo(),
            'getUserInfo() must return username if specified.'
        );
    }

    /**
     * getUserInfo() must return username and password separated with ":".
     * 
     * @depends test_construct_withUriString
     * @depends test_getUserInfo_withUsername
     */
    public function test_getUserInfo_withUsernameAndPassword()
    {
        $uri = new Uri('http://username:pass@localhost/my/path');
        $this->assertSame(
            'username:pass',
            $uri->getUserInfo(),
            'getUserInfo() must return username and password separated with ":".'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / withUserInfo()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * withUserInfo() must retain the state of the current instance.
     * 
     * @depends test_construct_withUriString
     * @depends test_getUserInfo_withoutInfo
     */
    public function test_withUserInfo_state()
    {
        $uri = new Uri('http://localhost');
        $uri2 = $uri->withUserInfo('user', 'pass');
        $this->assertSame(
            '',
            $uri->getUserInfo(),
            'withUserInfo() must retain the state of the current instance.'
        );
    }

    /**
     * withUserInfo() must return a new instance.
     * 
     * @depends test_construct_withUriString
     */
    public function test_withUserInfo_returnsNew()
    {
        $uri  = new Uri('http://localhost');
        $uri2 = $uri->withUserInfo('user', 'pass');
        $this->assertNotSame(
            $uri,
            $uri2,
            'withUserInfo() must return a new instance.'
        );
    }

    /**
     * withUserInfo() must apply specified username to the new instance and return it via getUserInfo().
     * 
     * @depends test_construct_withUriString
     * @depends test_getUserInfo_withUsernameAndPassword
     */
    public function test_withUserInfo_applyUser()
    {
        $uri = (new Uri('http://localhost'))->withUserInfo('user');
        $this->assertSame(
            'user',
            $uri->getUserInfo(),
            'withUserInfo() must apply specified username to the new instance and return it via getUserInfo().'
        );
    }

    /**
     * withUserInfo() must apply username and password to the new instance and return it via getUserInfo().
     * 
     * @depends test_construct_withUriString
     * @depends test_getUserInfo_withUsernameAndPassword
     */
    public function test_withUserInfo_applyUserAndPass()
    {
        $uri = (new Uri('http://localhost'))->withUserInfo('user', 'pass');
        $this->assertSame(
            'user:pass',
            $uri->getUserInfo(),
            'withUserInfo must apply username and password to the new instance and return it via getUserInfo().'
        );
    }

    /**
     * Provide an empty string to withUserInfo() method must be equivalent to removing userInfo.
     * 
     * @depends test_construct_withUriString
     * @depends test_getUserInfo_withUsernameAndPassword
     * @depends test_withUserInfo_applyUserAndPass
     * @depends test_getUserInfo_withoutInfo
     */
    public function test_withUserInfo_withEmptyString()
    {
        $uri = (new Uri('http://user:pass@test.com/'))->withUserInfo('');
        $this->assertSame(
            '',
            $uri->getUserInfo(),
            'Provide an empty string to withUserInfo() method must be equivalent to removing userInfo.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / getHost()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * getHost() must return an empty string if no host is present.
     * 
     * @depends test_construct_withUriString
     */
    public function test_getHost_withoutHost()
    {
        $uri = new Uri('/my/path');
        $this->assertSame(
            '',
            $uri->getHost(),
            'getHost() must return an empty string if no host is present.'
        );
    }

    /**
     * getHost() must return host.
     * 
     * @depends test_construct_withUriString
     */
    public function test_getHost_withHost()
    {
        $uri = new Uri('http://localhost/');
        $this->assertSame(
            'localhost',
            $uri->getHost(),
            'getHost() must return host.'
        );
    }

    /**
     * getHost() must return host normalized to lowercase.
     * 
     * @depends test_construct_withUriString
     * @depends test_getHost_withHost
     */
    public function test_getHost_normalizedToLowercase()
    {
        $uri = new Uri('http://My-DoMain.com/');
        $this->assertSame(
            'my-domain.com',
            $uri->getHost(),
            'getHost() must return host normalized to lowercase.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / withHost()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * withHost() must retain the state of the current instance.
     * 
     * @depends test_construct_withUriString
     * @depends test_getHost_withHost
     */
    public function test_withHost_state()
    {
        $uri = new Uri('http://test.com');
        $uri2 = $uri->withHost('localhost');
        $this->assertSame(
            'test.com',
            $uri->getHost(),
            'withHost() must retain the state of the current instance.'
        );
    }

    /**
     * withHost() must return a new instance.
     * 
     * @depends test_construct_withoutArgument
     */
    public function test_withHost_returnsNew()
    {
        $uri = new Uri();
        $uri2 = $uri->withHost('test.com');
        $this->assertNotSame(
            $uri,
            $uri2,
            'withHost() must return a new instance.'
        );
    }

    /**
     * withHost() method must apply new host and return it via getHost() method.
     * 
     * @depends test_construct_withoutArgument
     * @depends test_getHost_withHost
     */
    public function test_withHost_applyNewHost()
    {
        $uri = (new Uri())->withHost('localhost');
        $this->assertSame(
            'localhost',
            $uri->getHost(),
            'withHost() method must apply new host and return it via getHost() method.'
        );
    }

    /**
     * Provide an empty string to withHost() method must be equivalent to remove the host.
     * 
     * @depends test_construct_withUriString
     * @depends test_getHost_withoutHost
     */
    public function test_withHost_withEmptyString()
    {
        $uri = (new Uri('http://localhost'))->withHost('');
        $this->assertSame(
            '',
            $uri->getHost(),
            'Provide an empty string to withHost() method must be equivalent to remove the host.'
        );
    }

    /**
     * Provide an invalid host name to withHost() method must throw InvalidArgumentException.
     * 
     * @depends test_construct_withoutArgument
     * @depends test_withHost_applyNewHost
     */
    public function test_withHost_withInvalidArgument()
    {
        $this->expectException(InvalidArgumentException::class);
        (new Uri())->withHost('-test.com');
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / getPort()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * If no port is present getPort() must return null.
     * 
     * @depends test_construct_withUriString
     */
    public function test_getPort_withoutPort()
    {
        $uri = new Uri('http://localhost');
        $this->assertNull(
            $uri->getPort(),
            'If no port is present getPort() must return null.'
        );
    }

    /**
     * If specified port is the standard port for provided scheme, getPort() must not return that port.
     * Test with http scheme
     * 
     * @depends test_construct_withUriString
     * @depends test_getPort_withoutPort
     */
    public function test_getPort_withHttpDefaultPort()
    {
        $uri = new Uri('http://localhost:80');
        $this->assertNull(
            $uri->getPort(),
            'If default port is specified with "http" scheme (port 80), getPort() must not return that port.'
        );
    }

    /**
     * If specified port is the standard port for provided scheme, getPort() must not return that port.
     * Test with https scheme
     * 
     * @depends test_construct_withUriString
     * @depends test_getPort_withoutPort
     */
    public function test_getPort_withHttpsDefaultPort()
    {
        $uri = new Uri('https://localhost:443');
        $this->assertNull(
            $uri->getPort(),
            'If default port is specified with "https" scheme (port 443), getPort() must not return that port.'
        );
    }

    /**
     * If provided port is not the default port, getPort() must return that port.
     * 
     * @depends test_construct_withUriString
     */
    public function test_getPort_withPort()
    {
        $uri = new Uri('http://localhost:8000');
        $this->assertEquals(
            8000,
            $uri->getPort(),
            'If provided port is not the default port, getPort() must return that port.'
        );
    }

    /**
     * getPort() must return port as integer.
     * 
     * @depends test_construct_withUriString
     * @depends test_getPort_withPort
     */
    public function test_getPort_returnInteger()
    {
        $uri = new Uri('http://localhost:3000');
        $this->assertIsInt(
            $uri->getPort(),
            'getPort() must return port as integer.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / withPort()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * withPort() must retain the state of the current instance.
     * 
     * @depends test_construct_withUriString
     * @depends test_getPort_withPort
     */
    public function test_withPort_state()
    {
        $uri = new Uri('http://localhost:3000');
        $uri->withPort(8000);
        $this->assertSame(
            3000,
            $uri->getPort(),
            'withPort() must retain the state of the current instance.'
        );
    }

    /**
     * withPort() must return a new instance.
     * 
     * @depends test_construct_withUriString
     */
    public function test_withPort_returnsNew()
    {
        $uri = new Uri('http://localhost');
        $uri2 = $uri->withPort(3000);
        $this->assertNotSame(
            $uri,
            $uri2,
            'withPort() must return a new instance.'
        );
    }

    /**
     * withPort() must return provided port via getPort() method.
     * 
     * @depends test_construct_withUriString
     * @depends test_getPort_withPort
     */
    public function test_withPort_applyNewPort()
    {
        $uri = (new Uri('http://localhost'))->withPort(8000);
        $this->assertSame(
            8000,
            $uri->getPort(),
            'withPort() must return provided port via getPort() method.'
        );
    }

    /**
     * withPort() must raise InvalidArgumentException for port outside the established TCP and UDP port ranges.
     * 
     * @depends test_construct_withUriString
     */
    public function test_withPort_withOutOfRangePort()
    {
        $this->expectException(InvalidArgumentException::class);
        (new Uri('http://localhost'))->withPort(100000);
    }

    /**
     * withPort() must raise InvalidArgumentException if provided argument is negative int.
     * 
     * @depends test_construct_withUriString
     */
    public function test_withPort_withNegativePort()
    {
        $this->expectException(InvalidArgumentException::class);
        (new Uri('http://localhost'))->withPort(-1);
    }

    /**
     * Provide null to withPort() method must be equivalent to remove the port.
     * 
     * @depends test_construct_withUriString
     * @depends test_getPort_withoutPort
     */
    public function test_withPort_withNull()
    {
        $uri = (new Uri('http://localhost:8000'))->withPort(null);
        $this->assertNull(
            $uri->getPort(),
            'Provide null to withPort() method must be equivalent to remove the port.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / getPath()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * getPath() must return an empty string if no path is specified.
     * 
     * @depends test_construct_withUriString
     */
    public function test_getPath_withoutPath()
    {
        $uri = new Uri('http://localhost');
        $this->assertSame(
            '',
            $uri->getPath(),
            'getPath() must return an empty string if no path is specified.'
        );
    }

    /**
     * getPath() must return path.
     * 
     * @depends test_construct_withUriString
     */
    public function test_getPath_withPath()
    {
        $uri = new Uri('http://localhost/my/path');
        $this->assertSame(
            '/my/path',
            $uri->getPath(),
            'getPath() must return path.'
        );
    }

    /**
     * getPath() must not normalize path adding a slash.
     * 
     * @depends test_construct_withUriString
     * @depends test_getPath_withPath
     */
    public function test_getPath_withoutStartingSlash()
    {
        $uri = new Uri('my/path/');
        $this->assertSame(
            'my/path/',
            $uri->getPath(),
            'getPath() must not normalize path adding a slash.'
        );
    }

    /**
     * getPath() must return a percent-encoded path.
     * 
     * @depends test_construct_withUriString
     * @depends test_getPath_withPath
     */
    public function test_getPath_percentEncoded()
    {
        $uri = new uri('/my%/path');
        $this->assertSame(
            '/my%25/path',
            $uri->getPath(),
            'getPath() must return a percent-encoded path.'
        );
    }

    /**
     * getPath() must not return double percent-encoded path.
     * 
     * @depends test_construct_withUriString
     * @depends test_getPath_withPath
     * @depends test_getPath_percentEncoded
     */
    public function test_getPath_doublePercentEncoded()
    {
        $uri = new uri('/my%25/path');
        $this->assertSame(
            '/my%25/path',
            $uri->getPath(),
            'getPath() must not return double percent-encoded path.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / withPath()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * withPath() must retain the state of the current instance.
     * 
     * @depends test_construct_withUriString
     * @depends test_getPath_withPath
     */
    public function test_withPath_state()
    {
        $uri = new Uri('/my/path');
        $uri->withPath('/');
        $this->assertSame(
            '/my/path',
            $uri->getPath(),
            'withPath() must retain the state of the current instance.'
        );
    }

    /**
     * withPath() must return a new instance.
     * 
     * @depends test_construct_withUriString
     */
    public function test_withPath_returnNew()
    {
        $uri = new Uri('/my/path');
        $uri2 = $uri->withPath('/');
        $this->assertNotSame(
            $uri,
            $uri2,
            'withPath() must return a new instance.'
        );
    }

    /**
     * withPath() must apply path and return it via getPath() method.
     * 
     * @depends test_construct_withUriString
     * @depends test_getPath_withPath
     */
    public function test_withPath_applyNewPath()
    {
        $uri = (new Uri('http://localhost'))->withPath('/test');
        $this->assertSame(
            '/test',
            $uri->getPath(),
            'withPath() must apply path and return it via getPath() method.'
        );
    }

    /**
     * withPath() must accept empty path.
     * 
     * @depends test_construct_withoutArgument
     * @depends test_getPath_withoutPath
     * @depends test_withPath_applyNewPath
     */
    public function test_withPath_emptyPath()
    {
        $uri = (new Uri('http://localhost/my/path'))->withPath('');
        $this->assertSame(
            '',
            $uri->getPath(),
            'withPath() must accept empty path.'
        );
    }

    /**
     * withPath() must accept rootless path (not starting with a slash).
     * 
     * @depends test_construct_withoutArgument
     * @depends test_getPath_withPath
     * @depends test_withPath_applyNewPath
     */
    public function test_withPath_rootless()
    {
        $uri = (new Uri())->withPath('rootless/path');
        $this->assertSame(
            'rootless/path',
            $uri->getPath(),
            'withPath() must accept rootless path (not starting with a slash).'
        );
    }

    /**
     * withPath() must throw InvalidArgumentException if a rootless path is specified for a host-relative URI.
     * 
     * @depends test_construct_withUriString
     */
    public function test_withPath_applyRootlessToHostRelativeUri()
    {
        $this->expectException(InvalidArgumentException::class);
        (new Uri('http://localhost'))->withPath('rootless/path');
    }

    /**
     * Argument provided to withPath() must be percent-encoded.
     * 
     * @depends test_getPath_withPath
     * @depends test_withPath_applyNewPath
     * @depends test_getPath_percentEncoded
     */
    public function test_withPath_encoded()
    {
        $uri = (new Uri())->withPath('/my/pa%th');
        $this->assertSame(
            '/my/pa%25th',
            $uri->getPath(),
            'Argument provided to withPath() must be percent-encoded.'
        );
    }

    /**
     * Argument provided to withPath() must not be double encoded.
     * 
     * @depends test_getPath_withPath
     * @depends test_withPath_applyNewPath
     * @depends test_getPath_doublePercentEncoded
     */
    public function test_withPath_doubleEncoded()
    {
        $uri = (new Uri())->withPath('/my/pa%25th');
        $this->assertSame(
            '/my/pa%25th',
            $uri->getPath(),
            'Argument provided to withPath() must not be double encoded.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / getQuery()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * getQuery() must return an empty string if no query is present.
     * 
     * @depends test_construct_withoutArgument
     */
    public function test_getQuery_withoutQuery()
    {
        $uri = new Uri();
        $this->assertSame(
            '',
            $uri->getQuery(),
            'getQuery() must return an empty string if no query is present.'
        );
    }

    /**
     * getQuery() must not return The leading "?".
     * 
     * @depends test_construct_withUriString
     */
    public function test_getQuery_leadingCharacter()
    {
        $uri = new Uri('/my/path?my=query');
        $this->assertStringNotContainsString(
            '?',
            $uri->getQuery(),
            'getQuery() must not return The leading "?".'
        );
    }

    /**
     * getQuery() must return query string.
     * 
     * @depends test_construct_withUriString
     * @depends test_getQuery_leadingCharacter
     */
    public function test_getQuery_withQuery()
    {
        $uri = new Uri('http://localhost/?my=query&test=test');
        $this->assertSame(
            'my=query&test=test',
            $uri->getQuery(),
            'getQuery() must return query string.'
        );
    }

    /**
     * getQuery() must percent-encode query string.
     * 
     * @depends test_construct_withUriString
     * @depends test_getQuery_leadingCharacter
     * @depends test_getQuery_withQuery
     */
    public function test_getQuery_encode()
    {
        $uri = new Uri('/?param=to%encode');
        $this->assertSame(
            'param=to%25encode',
            $uri->getQuery(),
            'getQuery() must percent-encode query string.'
        );
    }

    /**
     * getQuery() must not double encode query string.
     * 
     * @depends test_construct_withUriString
     * @depends test_getQuery_leadingCharacter
     * @depends test_getQuery_withQuery
     * @depends test_getQuery_encode
     */
    public function test_getQuery_doubleEncode()
    {
        $uri = new Uri('/?param=to%25encode');
        $this->assertSame(
            'param=to%25encode',
            $uri->getQuery(),
            'getQuery() must not double encode query string.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / withQuery()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * withQuery() must retain the state of the current instance.
     * 
     * @depends test_construct_withUriString
     * @depends test_getQuery_withQuery
     */
    public function test_withQuery_state()
    {
        $uri = new Uri('/?param=value');
        $uri->withQuery('param=newvalue');
        $this->assertSame(
            'param=value',
            $uri->getQuery(),
            'withQuery() must retain the state of the current instance.'
        );
    }

    /**
     * withQuery() must return a new instance.
     * 
     * @depends test_construct_withoutArgument
     */
    public function test_withQuery_returnNew()
    {
        $uri = new Uri();
        $uri2 = $uri->withQuery('my=test');
        $this->assertNotSame(
            $uri,
            $uri2,
            'withQuery() must return a new instance.'
        );
    }

    /**
     * withQuery() must apply the new query and return it via getQuery() method.
     * 
     * @depends test_construct_withUriString
     * @depends test_getQuery_withQuery
     */
    public function test_withQuery_applyNewQuery()
    {
        $uri = (new Uri('http://test.com?param=value'))->withQuery('test=value');
        $this->assertSame(
            'test=value',
            $uri->getQuery(),
            'withQuery() must apply the new query and return it via getQuery() method.'
        );
    }

    /**
     * Provide an empty string to withQuery() method must be equivalent to removing the query.
     * 
     * @depends test_construct_withUriString
     * @depends test_getQuery_withoutQuery
     */
    public function test_withQuery_withEmptyString()
    {
        $uri = (new Uri('http://localhost/?param=value'))->withQuery('');
        $this->assertSame(
            '',
            $uri->getQuery(),
            'Provide an empty string to withQuery() method must be equivalent to removing the query.'
        );
    }

    /**
     * withQuery() provided argument must be percent-encoded.
     * 
     * @depends test_construct_withUriString
     * @depends test_getQuery_withQuery
     * @depends test_getQuery_encode
     */
    public function test_withQuery_encode()
    {
        $uri = (new Uri('/'))->withQuery('my=que%ry');
        $this->assertSame(
            'my=que%25ry',
            $uri->getQuery(),
            'withQuery() provided argument must be percent-encoded.'
        );
    }

    /**
     * withQuery() must not double encode provided query.
     * 
     * @depends test_construct_withUriString
     * @depends test_getQuery_withQuery
     * @depends test_getQuery_doubleEncode
     */
    public function test_withQuery_doubleEncode()
    {
        $uri = (new Uri('/'))->withQuery('my=que%25ry');
        $this->assertSame(
            'my=que%25ry',
            $uri->getQuery(),
            'withQuery() provided argument must be percent-encoded.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / getFragment()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * getFragment() must return an empty string if no fragment is present.
     * 
     * @depends test_construct_withoutArgument
     */
    public function test_getFragment_withoutFragment()
    {
        $uri = new Uri();
        $this->assertSame(
            '',
            $uri->getFragment(),
            'getFragment() must return an empty string if no fragment is present.'
        );
    }

    /**
     * getFragment() must not return the leading "#".
     * 
     * @depends test_construct_withUriString
     */
    public function test_getFragment_leadingCharacter()
    {
        $uri = new Uri('/my/path#fragment');
        $this->assertStringNotContainsString(
            '#',
            $uri->getFragment(),
            'getFragment() must not return the leading "#".'
        );
    }

    /**
     * getFragment() must return fragment.
     * 
     * @depends test_construct_withUriString
     * @depends test_getFragment_leadingCharacter
     */
    public function test_getFragment_withFragment()
    {
        $uri = new Uri('/my/path#fragment');
        $this->assertSame(
            'fragment',
            $uri->getFragment(),
            'getFragment() must return fragment.'
        );
    }

    /**
     * getFragment() must percent-encode fragment.
     * 
     * @depends test_construct_withUriString
     * @depends test_getFragment_leadingCharacter
     * @depends test_getFragment_withFragment
     */
    public function test_getFragment_encode()
    {
        $uri = new Uri('/#frag%ment');
        $this->assertSame(
            'frag%25ment',
            $uri->getFragment(),
            'getFragment() must percent-encode fragment.'
        );
    }

    /**
     * getFragment() must not double encode fragment string.
     * 
     * @depends test_construct_withUriString
     * @depends test_getFragment_leadingCharacter
     * @depends test_getFragment_withFragment
     * @depends test_getFragment_encode
     */
    public function test_getFragment_doubleEncode()
    {
        $uri = new Uri('/#frag%25ment');
        $this->assertSame(
            'frag%25ment',
            $uri->getFragment(),
            'getFragment() must not double encode fragment string.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / withFragment()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * withFragment() must retain the state of the current instance.
     * 
     * @depends test_construct_withUriString
     * @depends test_getFragment_withFragment
     */
    public function test_withFragment_state()
    {
        $uri = new Uri('/test#fragment');
        $uri->withFragment('newfrag');
        $this->assertSame(
            'fragment',
            $uri->getFragment(),
            'withFragment() must retain the state of the current instance.'
        );
    }

    /**
     * withFragment() must return a new instance.
     * 
     * @depends test_construct_withUriString
     */
    public function test_withFragment_returnsNew()
    {
        $uri = new Uri('/');
        $uri2 = $uri->withFragment('fragment');
        $this->assertNotSame(
            $uri,
            $uri2,
            'withFragment() must return a new instance.'
        );
    }

    /**
     * withFragment() must apply provided fragment and return it via getFragment() method.
     * 
     * @depends test_construct_withUriString
     * @depends test_getFragment_withFragment
     */
    public function test_withFragment_applyNewFragment()
    {
        $uri = (new Uri('http://localhost/'))->withFragment('fragment');
        $this->assertSame(
            'fragment',
            $uri->getFragment(),
            'withFragment() must apply provided fragment and return it via getFragment() method.'
        );
    }

    /**
     * Provide an empty string to withFragment() is equivalent to remove the fragment.
     * 
     * @depends test_construct_withUriString
     * @depends test_getFragment_withoutFragment
     * @depends test_withFragment_applyNewFragment
     */
    public function test_withFragment_withEmptyString()
    {
        $uri = (new Uri('http://localhost/#fragment'))->withFragment('');
        $this->assertSame(
            '',
            $uri->getFragment(),
            'Provide an empty string to withFragment() is equivalent to remove the fragment.'
        );
    }

    /**
     * withFragment() must percent-encode provided fragment.
     * 
     * @depends test_construct_withUriString
     * @depends test_getFragment_withFragment
     * @depends test_withFragment_applyNewFragment
     */
    public function test_withFragment_encode()
    {
        $uri = (new Uri('http://localhost/'))->withFragment('fra%gment');
        $this->assertSame(
            'fra%25gment',
            $uri->getFragment(),
            'withFragment() must percent-encode provided fragment.'
        );
    }

    /**
     * withFragment() must not double encode provided fragment.
     * 
     * @depends test_construct_withUriString
     * @depends test_getFragment_withFragment
     * @depends test_withFragment_applyNewFragment
     * @depends test_withFragment_encode
     */
    public function test_withFragment_doubleEncode()
    {
        $uri = (new Uri('http://localhost/'))->withFragment('fra%25gment');
        $this->assertSame(
            'fra%25gment',
            $uri->getFragment(),
            'withFragment() must not double encode provided fragment.'
        );
    }
}
