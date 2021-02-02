<?php

namespace Chiphpmunk\Http;

use InvalidArgumentException;

class Request implements RequestInterface
{
    use MessageTrait;
    
    // =================================================================================================================
    //
    //      ATTRIBUTES
    //
    // =================================================================================================================

    /**
     * @var string|null $target HTTP request target
     */
    private $target;

    /**
     * @var string $method Request method
     */
    private $method = 'GET';

    /**
     * @var UriInterface $uri Request URI
     */
    private $uri;

    // =================================================================================================================
    //
    //      METHODS
    //
    // =================================================================================================================

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > REQUEST METHOD
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return string The request method
     * If no method was provided (via withMethod()), this method returns 'GET'.
     */
    public function getMethod() : string
    {
        return $this->method;
    }

    /**
     * Returns an instance with the provided HTTP method.
     *
     * @param string $method Case-sensitive method.
     * 
     * @throws InvalidArgumentException for invalid HTTP methods.
     * 
     * @return RequestInterface A static instance with the provided HTTP method
     */
    public function withMethod(string $method) : RequestInterface
    {
        $request = clone $this;
        $request->setMethod($method);
        return $request;
    }

    /**
     * Sets request method
     *
     * @param string $method Case-sensitive method.
     * 
     * @throws InvalidArgumentException for invalid HTTP methods.
     *
     * @return void
     */
    private function setMethod(string $method) : void
    {
        if ($method === '' || preg_match('`\s`', $method)) {
            throw new InvalidArgumentException('Request method cannot be empty and must not contain white spaces.');
        }
        $this->method = $method;
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > REQUEST URI
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @see http://tools.ietf.org/html/rfc3986#section-4.3
     * 
     * @return UriInterface The request URI
     */
    public function getUri() : UriInterface
    {
        if ($this->uri === null) {
            $this->uri = new Uri();
        }
        return $this->uri;
    }

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
    public function withUri(UriInterface $uri, bool $preserveHost = false) : RequestInterface
    {
        if ($uri === $this->uri) {
            return $this;
        }
        $request = clone $this;
        $request->uri = $uri;
        if ((
                $preserveHost
                && (!$request->hasHeader('host') || empty($request->getHeader('host')))
                && $request->getUri()->getHost()
            ) || (
                !$preserveHost && $request->getUri()->getHost()
            )
        ) {
            $request->setHostHeaderFromUri();
        }
        return $request;
    }

    /**
     * Sets Host header from current URI
     *
     * @return void
     */
    private function setHostHeaderFromUri()
    {
        if ($this->getUri()->getHost()) {
            $this->setHeader(
                'Host',
                $this->getUri()->getPort()
                    ? $this->getUri()->getHost() . ':' .  $this->getUri()->getPort()
                    : $this->getUri()->getHost()
            );
        }
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > REQUEST TARGET
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return string Request target
     * If no URI is available, and no request-target has been specifically
     * provided, this method returns the string "/".
     */
    public function getRequestTarget() : string
    {
        if ($this->target === null && $this->uri !== null) {
            $path  = $this->uri->getPath()  === '' ? '/' : $this->uri->getPath();
            $query = $this->uri->getQuery() === '' ? ''  : '?' . $this->uri->getQuery();
            $this->target = $path . $query;
        }
        return $this->target === null ? '/' : $this->target;
    }

    /**
     * Returns an instance with the specified request target.
     *
     * @see http://tools.ietf.org/html/rfc7230#section-5.3
     * 
     * @param string $requestTarget The request target
     * 
     * @return RequestInterface A static instance with specified request target
     */
    public function withRequestTarget(string $requestTarget) : RequestInterface
    {
        $request = clone $this;
        $request->target = $requestTarget;
        return $request;
    }
}
