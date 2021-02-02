<?php

namespace Chiphpmunk\Http;

use InvalidArgumentException;

interface ResponseInterface extends MessageInterface
{
    /**
     * @return int The HTTP response status code.
     */
    public function getStatusCode() : int;

    /**
     * @see http://tools.ietf.org/html/rfc7231#section-6
     * @see http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * 
     * @return string The HTTP response status reason phrase
     */
    public function getReasonPhrase() : string;

    /**
     * Returns an instance with the specified status code and, optionally, reason phrase.
     *
     * If no reason phrase is specified, implementations will choose to default
     * to the RFC 7231 or IANA recommended reason phrase for the response's
     * status code.
     *
     * @see http://tools.ietf.org/html/rfc7231#section-6
     * @see http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * 
     * @param int    $code         The 3-digit integer result code to set.
     * @param string $reasonPhrase The reason phrase to use.
     * 
     * @throws InvalidArgumentException For invalid status code arguments
     * 
     * @return ResponseInterface A static instance with the provided status
     */
    public function withStatus(int $code, string $reasonPhrase = '') : ResponseInterface;

    /**
     * Sends HTTP response
     * 
     * @return void
     */
    public function send() : void;
}
