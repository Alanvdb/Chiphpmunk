<?php

namespace Chiphpmunk\Http;

use Chiphpmunk\Http\MessageTrait;

use InvalidArgumentException;

class Response implements ResponseInterface
{
    use MessageTrait;

    // =================================================================================================================
    //
    //      ATTRIBUTES
    //
    // =================================================================================================================

    /**
     * @const string[] STATUS HTTP response status
     *
     * @see https://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     */
    const STATUS = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        103 => 'Early Hints',

        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',

        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',

        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Too Early',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',

        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required'
    ];

    /**
     * @var int $statusCode HTTP response status code
     */
    private $statusCode = 200;

    /**
     * @var string $statusMessage HTTP response status message
     */
    private $phrase = 'OK';

    // =================================================================================================================
    //
    //      METHODS
    //
    // =================================================================================================================

    /**
     * Constructor
     * 
     * @param string $content Response body content
     */
    public function __construct(string $content = '')
    {
        if ($content !== '') {
            $this->getBody()->write($content);
        }
    }

    /**
     * @return int The HTTP response status code.
     */
    public function getStatusCode() : int
    {
        return $this->statusCode;
    }

    /**
     * @see http://tools.ietf.org/html/rfc7231#section-6
     * @see http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * 
     * @return string The HTTP response status reason phrase
     */
    public function getReasonPhrase() : string
    {
        return $this->phrase;
    }

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
    public function withStatus(int $code, string $reasonPhrase = '') : ResponseInterface
    {
        if (!isset(self::STATUS[$code])) {
            throw new InvalidArgumentException('Unknown HTTP response status code.');
        }
        if ($reasonPhrase === '') {
            $reasonPhrase = self::STATUS[$code];
        }
        if ($code === $this->statusCode && $reasonPhrase === $this->phrase) {
            return $this;
        }
        $response = clone $this;
        $response->statusCode = $code;
        $response->phrase = $reasonPhrase;
        return $response;
    }

    /**
     * Sends HTTP response
     * 
     * @return void
     */
    public function send() : void
    {
        if (headers_sent()) {
            return;
        }
        header(
            "HTTP/{$this->getProtocolVersion()} {$this->getStatusCode()} {$this->getReasonPhrase()}",
            true,
            $this->getStatusCode()
        );
        foreach (array_keys($this->getHeaders()) as $name) {
            header($name . ': ' . $this->getHeaderLine($name), false);
        }
        $body = $this->getBody();

        if ($body->isSeekable()) {
            $body->rewind();
        }

        while(!$body->eof()) {
            echo $body->read(8192);
        }

        exit();
    }
}
