<?php

namespace Chiphpmunk\Http;

use InvalidArgumentException;

interface ServerRequestInterface extends RequestInterface
{
    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > SERVER PARAMETERS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return array The server parameters
     */
    public function getServerParams() : array;

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > QUERY
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return array The deserialized query string arguments, if any.
     * The query params might not be in sync with the URI or server
     * params. If you need to ensure you are only getting the original
     * values, you may need to parse the query string from `getUri()->getQuery()`
     * or from the `QUERY_STRING` server param.
     */
    public function getQueryParams() : array;

    /**
     * Return an instance with the specified query string arguments.
     *
     * These MAY be injected during instantiation.
     * Setting query string arguments will not change the URI stored by the
     * request, nor the values in the server params.
     *
     * @param array $query Array of query string arguments, typically from $_GET.
     * 
     * @return ServerRequestInterface A static instance with provided query parameters
     */
    public function withQueryParams(array $query) : ServerRequestInterface;

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > COOKIES
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return array The cookies sent by the client to the server
     */
    public function getCookieParams() : array;

    /**
     * Return an instance with the specified cookies.
     * 
     * These MAY be injected during instantiation.
     * This method do not update the related Cookie header of the request
     * instance, nor related values in the server params.
     *
     * @param array $cookies Array of key/value pairs representing cookies.
     * 
     * @return ServerRequestInterface A static instance with provided cookie parameters
     */
    public function withCookieParams(array $cookies) : ServerRequestInterface;

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > UPLOADED FILES
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return array An array tree of UploadedFileInterface instances
     */
    public function getUploadedFiles() : array;

    /**
     * Create a new instance with the specified uploaded files.
     * 
     * These MAY be injected during instantiation.
     *
     * @param array $uploadedFiles An array tree of UploadedFileInterface instances.
     * 
     * @throws InvalidArgumentException if an invalid structure is provided.
     * 
     * @return ServerRequestInterface A static instance with provided uploaded files
     * The body of the returned instance has updated parameters.
     */
    public function withUploadedFiles(array $uploadedFiles) : ServerRequestInterface;

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > BODY
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return null|array|object The deserialized body parameters, if any.
     * If the request Content-Type is either application/x-www-form-urlencoded
     * or multipart/form-data, and the request method is POST, this method will
     * return the contents of $_POST.
     */
    public function getParsedBody();

    /**
     * Returns an instance with the specified body parameters.
     *
     * These MAY be injected during instantiation.
     *
     * @param null|array|object $data The deserialized body data
     * 
     * @throws InvalidArgumentException if an unsupported argument type is provided.
     * 
     * @return ServerRequestInterface A static instance with the specified parsed body
     */
    public function withParsedBody($data) : ServerRequestInterface;

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > ATTRIBUTES
    // -----------------------------------------------------------------------------------------------------------------
    
    /**
     * @return mixed[] Attributes derived from the request.
     */
    public function getAttributes() : array;

    /**
     * Returns specified request attribute.
     *
     * @param string $name    Name of the attribute to return value
     * @param mixed  $default Default value to return if the attribute does not exist.
     * 
     * @return mixed
     */
    public function getAttribute(string $name, $default = null);

    /**
     * Returns the request with provided attribute
     *
     * @param string $name  Attribute name.
     * @param mixed  $value The value of the attribute.
     * 
     * @return self The request with provided attribute
     */
    public function withAttribute(string $name, $value) : ServerRequestInterface;

    /**
     * Returns the request without the specified attribute.
     *
     * @param string $name The attribute name
     * 
     * @return self The request without specified attribute
     */
    public function withoutAttribute(string $name) : ServerRequestInterface;
}
