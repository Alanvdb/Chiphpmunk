<?php

namespace Chiphpmunk\Http;

use Chiphpmunk\Stream\StreamInterface;

interface MessageInterface
{
    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > PROTOCOL VERSION
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return string HTTP protocol version number
     */
    public function getProtocolVersion() : string;

    /**
     * Returns an instance with the provided HTTP protocol version.
     * 
     * @param string $version HTTP protocol version number like "1.1", "2.0", ...
     * 
     * @throws InvalidArgumentException If cannot retrieve the protocol version number from provided string
     * 
     * @return MessageInterface
     */
    public function withProtocolVersion(string $version) : MessageInterface;

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
    public function hasHeader(string $name) : bool;

    /**
     * @return string[][] HTTP message headers in an array like ['Host' => [0 => 'my-domain.com'], ...]
     */
    public function getHeaders() : array;

    /**
     * Returns header values of provided header name (case insensitive)
     *
     * @param string $name Header name searched using a case insensitive string comparison
     * 
     * @return string[]|false Header values or false if specified header does not exist
     */
    public function getHeader(string $name);

    /**
     * Returns specified header values as string concatenated together using a comma.
     * 
     * @param string $name Header name searched using a case insensitive string comparison
     * 
     * @return string header values as string concatenated together using a comma
     * If specified header does not exist, this method returns an empty string.
     */
    public function getHeaderLine(string $name) : string;

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
    public function withHeader(string $name, $value) : MessageInterface;

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
    public function withAddedHeader(string $name, $value) : MessageInterface;

    /**
     * Returns an instance without specified header
     * 
     * @param string $name Header name
     * 
     * @return MessageInterface A Message instance without specified header
     */
    public function withoutHeader(string $name) : MessageInterface;

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > BODY
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @throws RuntimeException if cannot open temporary stream
     * 
     * @return StreamInterface Body contents as stream
     */
    public function getBody() : StreamInterface;

    /**
     * Returns a new instance with specified body
     * 
     * @param StreamInterface $body Body contents as stream
     * 
     * @return MessageInterface A new instance with specified body 
     */
    public function withBody(StreamInterface $body) : MessageInterface;
}
