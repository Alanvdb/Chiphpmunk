<?php

namespace Chiphpmunk\Http;

use Chiphpmunk\Stream\StreamInterface;
use Chiphpmunk\Stream\Stream;

use Exception;
use RuntimeException;
use InvalidArgumentException;

trait MessageTrait
{
    // =================================================================================================================
    //
    //      ATTRIBUTES
    //
    // =================================================================================================================

    /**
     * @var string $protocolVersion HTTP protocol version number
     */
    private $protocolVersion = 1.1;

    /**
     * @var string[][] $headers HTTP message headers like ['Host' => [0 => 'my-domain.com'], ...]
     */
    private $headers = [];

    /**
     * @var string[] $headerNames To retrieve header names in a case insensitive manner like ['host' => 'Host']
     */
    private $headerNames = [];

    /**
     * @var StreamInterface $body Body content as stream
     */
    private $body;

    // =================================================================================================================
    //
    //      METHODS
    //
    // =================================================================================================================

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > PROTOCOL VERSION
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return string HTTP protocol version number
     */
    public function getProtocolVersion() : string
    {
        return $this->protocolVersion;
    }

    /**
     * Returns an instance with the provided HTTP protocol version.
     * 
     * @param string $version HTTP protocol version number like "1.1", "2.0", ...
     * 
     * @throws InvalidArgumentException If cannot retrieve the protocol version number from provided string
     * 
     * @return MessageInterface
     */
    public function withProtocolVersion(string $version) : MessageInterface
    {
        if (!preg_match('`([0-9]\.[0-9])`', $version, $matches)) {
            throw new InvalidArgumentException('Cannot retrieve the HTTP protocol version number from provided argument (like "1.1", "2.0", ...).');
        }
        if ($this->getProtocolVersion() === $matches[1]) {
            return $this;
        }
        $clone = clone $this;
        $clone->protocolVersion = $matches[1];
        return $clone;
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > HEADERS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Checks if provided header exists using a case insensitive string comparison.
     * 
     * @param string $name Header name
     * 
     * @return bool Whether the provided header name exists using a case insensitive string comparison.
     */
    public function hasHeader(string $name) : bool
    {
        return isset($this->headerNames[strtolower($name)]);
    }

    /**
     * @return string[][] HTTP message headers in an array like ['Host' => [0 => 'my-domain.com'], ...]
     */
    public function getHeaders() : array
    {
        return $this->headers;
    }

    /**
     * Returns header values of provided header name (case insensitive)
     *
     * @param string $name Header name searched using a case insensitive string comparison
     * 
     * @return string[]|false Header values or false if specified header does not exist
     */
    public function getHeader(string $name)
    {
        if ($this->hasHeader($name)) {
            return $this->headers[$this->headerNames[strtolower($name)]];
        }
        return false;
    }

    /**
     * Returns specified header values as string concatenated together using a comma.
     * 
     * @param string $name Header name searched using a case insensitive string comparison
     * 
     * @return string header values as string concatenated together using a comma
     * If specified header does not exist, this method returns an empty string.
     */
    public function getHeaderLine(string $name) : string
    {
        return $this->hasHeader($name) ? implode(',', $this->getHeader($name)) : '';
    }

    /**
     * Returns an instance with the provided value replacing the specified header.
     * 
     * @param string          $name  Header name
     * @param string|string[] $value Header value(s)
     * 
     * @throws InvalidArgumentException For invalid name or value
     * 
     * @return MessageInterface A new MessageInterface instance with replaced header
     */
    public function withHeader(string $name, $value) : MessageInterface
    {
        $clone = clone $this;
        $clone->setHeader($name, $value);
        return $clone;
    }

    /**
     * Returns an instance with the specified value appended to the specified header.
     * 
     * @param string          $name  Header name
     * @param string|string[] $value Header value(s)
     * 
     * @throws InvalidArgumentException For invalid name or value
     * 
     * @return MessageInterface A new MessageInterface instance with appended header value
     */
    public function withAddedHeader(string $name, $value) : MessageInterface
    {
        if (!$this->hasHeader($name)) {
            return $this->withHeader($name, $value);
        }
        if (!is_array($value)) {
            $value = [$value];
        }
        $newValues = $this->getHeader($name);
        foreach ($value as $val) {
            if (!in_array($val, $newValues)) {
                $newValues[] = $val;
            }
        }
        return $this->withHeader($name, $newValues);
    }

    /**
     * Returns an instance without specified header
     * 
     * @param string $name Header name
     * 
     * @return MessageInterface A Message instance without specified header
     */
    public function withoutHeader(string $name) : MessageInterface
    {
        if (!$this->hasHeader($name)) {
            return $this;
        }
        $clone = clone $this;
        unset($clone->headers[$clone->headerNames[strtolower($name)]]);
        unset($clone->headerNames[strtolower($name)]);
        return $clone;
    }

    /**
     * Sets header
     * 
     * @param string          $name  Header name
     * @param string|string[] $value Header value(s)
     * 
     * @throws InvalidArgumentException For invalid name or value
     * 
     * @return void
     */
    protected function setHeader(string $name, $value) : void
    {
        if ($name === '') {
            throw new InvalidArgumentException('Invalid HTTP message header name: name cannot be empty.');
        }
        if (!is_array($value)) {
            $value = [$value];
        }
        foreach ($value as $val) {
            if (!is_string($val)) {
                throw new InvalidArgumentException('$value argument must be of type string or an array of string values.');
            }
        }
        if ($this->hasHeader($name)) {
            unset($this->headers[$this->headerNames[strtolower($name)]]);
        }
        $this->headerNames[strtolower($name)] = $name;
        $this->headers[$name] = $value;
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > BODY
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @throws RuntimeException if cannot open temporary stream
     * 
     * @return StreamInterface Body contents as stream
     */
    public function getBody() : StreamInterface
    {
        if ($this->body === null) {
            try {
                $this->body = (new Stream(fopen('php://temp', 'r+')));
            } catch (Exception $e) {
                throw new RuntimeException('An error occured while opening a temporary stream.');
            }
        }
        return $this->body;
    }

    /**
     * Returns a new instance with specified body
     * 
     * @param StreamInterface $body Body contents as stream
     * 
     * @return MessageInterface A new instance with specified body 
     */
    public function withBody(StreamInterface $body) : MessageInterface
    {
        $clone = clone $this;
        $clone->body = $body;
        return $clone;
    }
}
