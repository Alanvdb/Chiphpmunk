<?php

namespace Chiphpmunk\Http;

use InvalidArgumentException;

interface RequestInterface extends MessageInterface
{
    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > METHOD
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return string The request method
     * If no method was provided (via withMethod()), this method returns 'GET'.
     */
    public function getMethod() : string;

    /**
     * Returns an instance with the provided HTTP method.
     *
     * @param string $method Case-sensitive method.
     * 
     * @throws InvalidArgumentException for invalid HTTP methods.
     * 
     * @return RequestInterface A static instance with the provided HTTP method
     */
    public function withMethod(string $method) : RequestInterface;

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > REQUEST URI
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @see http://tools.ietf.org/html/rfc3986#section-4.3
     * 
     * @return UriInterface The request URI
     */
    public function getUri() : UriInterface;

    /**
     * Returns an instance with the provided URI.
     *
     * By default, this method updates the host header if the provided URI contains host component.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.3
     * 
     * @param UriInterface $uri          URI to use
     * @param bool         $preserveHost Preserve the original state of the Host header
     * If the original request has no host set, this argument has no effect.
     * 
     * @return RequestInterface A static RequestInterface instance with provided URI
     */
    public function withUri(UriInterface $uri, bool $preserveHost = false) : RequestInterface;

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > REQUEST TARGET
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return string Request target
     * If no URI is available, and no request-target has been specifically
     * provided, this method returns the string "/".
     */
    public function getRequestTarget() : string;

    /**
     * Returns an instance with the specified request target.
     *
     * @see http://tools.ietf.org/html/rfc7230#section-5.3
     * 
     * @param string $requestTarget The request target
     * 
     * @return RequestInterface A static instance with specified request target
     */
    public function withRequestTarget(string $requestTarget) : RequestInterface;
}
