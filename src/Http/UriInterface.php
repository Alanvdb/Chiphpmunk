<?php

namespace Chiphpmunk\Http;

interface UriInterface
{
    /**
     * Constructor
     *
     * @param string $uri URI in string format
     *
     * @throws InvalidArgumentException For malformed URI or unsupported URI components
     */
    public function __construct(string $uri = '');

    /**
     * Returns URI string value
     *
     * @return string URI in string format
     */
    public function __toString() : string;

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > SCHEME COMPONENT
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Returns scheme URI component
     *
     * Returned scheme is normalized to lowercase.
     * If no scheme is present this method returns an empty string.
     * The trailing ":" character is not part of the scheme and is not added.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     *
     * @return string
     */
    public function getScheme() : string;

    /**
     * Returns an instance with the specified scheme.
     *
     * An empty scheme is equivalent to removing the scheme.
     *
     * @param string $scheme The scheme to use with the new instance.
     *
     * @throws InvalidArgumentException for invalid or unsupported schemes.
     *
     * @return UriInterface A static instance with the specified scheme.
     */
    public function withScheme(string $scheme = '') : UriInterface;

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > AUTHORITY COMPONENT
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Returns authority URI component
     *
     * If no authority is present this method returns an empty string.
     * If the port URI component is not set or is the standard port for the current scheme, it is not included.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2
     *
     * @return string Authority URI component, in "[user-info@]host[:port]" format.
     */
    public function getAuthority() : string;

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > AUTHORITY COMPONENT > USER INFO
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Returns User informations URI component
     *
     * If no user informations are present, this method returns an empty string
     *
     * @return string User informations URI component, in "username[:password]" format.
     */
    public function getUserInfo() : string;

    /**
     * Returns an instance with the specified user information.
     *
     * An empty string for the user is equivalent to removing user information.
     *
     * @param string      $user     The user name to use for authority.
     * @param null|string $password The password associated with $user.
     * 
     * @throws InvalidArgumentException If no host is set
     *
     * @return UriInterface A static instance with the specified user information.
     */
    public function withUserInfo(string $user, ?string $password = null) : UriInterface;

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > AUTHORITY COMPONENT > HOST
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Returns host URI component
     *
     * Returned host is normalized to lowercase.
     * If no host URI component is present, this method returns an empty string.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-3.2.2
     *
     * @return string
     */
    public function getHost() : string;

    /**
     * Return an instance with the specified host.
     *
     * An empty host value is equivalent to removing the host.
     *
     * @param string $host The hostname to use with the new instance.
     *
     * @throws InvalidArgumentException for invalid hostnames.
     *
     * @return UriInterface A static instance with specified host.
     */
    public function withHost(string $host) : UriInterface;

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > AUTHORITY COMPONENT > PORT
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Returns port URI component
     *
     * This method returns null if URI uses the standard port.
     *
     * @return int|null
     */
    public function getPort() : ?int;

    /**
     * Return an instance with the specified port.
     *
     * A null value provided for the port is equivalent to removing the port information.
     *
     * @param null|int $port The port to use with the new instance; a null value
     *     removes the port information.
     *
     * @throws InvalidArgumentException for invalid ports (outside the established TCP and UDP port ranges).
     *
     * @return UriInterface A static instance with specified port
     */
    public function withPort(?int $port) : UriInterface;

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > PATH COMPONENT
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.3
     *
     * @return string The path URI component (can be empty)
     */
    public function getPath() : string;

    /**
     * Returns an instance with the specified path.
     *
     * @param string $path The path to use with the new instance.
     * The path can either be empty or absolute (starting with a slash) or
     * rootless (not starting with a slash).
     * If an HTTP path is intended to be host-relative rather than path-relative
     * then it must begin with a slash ("/"). HTTP paths not starting with a slash
     * are assumed to be relative to some base path known to the application or
     * consumer.
     * Users can provide both encoded and decoded path characters.
     *
     * @throws InvalidArgumentException for invalid paths.
     *
     * @return UriInterface A static instance with the specified path.
     */
    public function withPath(string $path) : UriInterface;

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > QUERY COMPONENT
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.4
     *
     * @return string The query string
     * If no query URI component is present, this method returns an empty string.
     * The leading "?" character is not part of the query and is not added.
     */
    public function getQuery(): string;

    /**
     * Returns an instance with the specified query string.
     * 
     * @param string $query The query string to use with the new instance.
     * Users can provide both encoded and decoded query characters.
     * An empty query string value is equivalent to removing the query string.
     *
     * @return UriInterface A static instance with the specified query string.
     */
    public function withQuery(string $query) : UriInterface;

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > FRAGMENT COMPONENT
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.5
     *
     * @return string The fragment component
     * If no fragment URI component is present, this method returns an empty string.
     * The leading "#" character is not part of the fragment and is not added.
     */
    public function getFragment() : string;

    /**
     * Returns an instance with the specified URI fragment.
     *
     * @param string $fragment The fragment to use with the new instance.
     * Users can provide both encoded and decoded fragment characters.
     * An empty fragment value is equivalent to removing the fragment.
     *
     * @return UriInterface A static instance with the specified fragment.
     */
    public function withFragment(string $fragment) : UriInterface;
}
