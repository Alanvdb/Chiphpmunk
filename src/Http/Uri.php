<?php

namespace Chiphpmunk\Http;

use InvalidArgumentException;

class Uri implements UriInterface
{
    // =================================================================================================================
    //
    //      CONSTANT ATTRIBUTES
    //
    // =================================================================================================================

    /**
     * @const string SUB_DELIMS URI sub delimiters for use in a regex
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2.2
     */
    private const SUB_DELIMS = '\$!&\'\(\)\*\+,;=';

    /**
     * @const string UNRESERVED_CHARS URI unreserved characters for use in a regex
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2.3
     */
    private const UNRESERVED_CHARS = 'a-zA-Z0-9\-\._~';

    /**
     * @const string SCHEME_PATTERN Scheme URI component pattern
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     */
    private const SCHEME_PATTERN = '`^[a-z][a-z0-9+-\.]*$`';

    /**
     * @const string ALLOWED_CHARS_IN_USERINFO
     *     Allowed characters in username and password URI components for use in a regex
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2.1
     */
    private const ALLOWED_CHARS_IN_USERINFO = self::UNRESERVED_CHARS . self::SUB_DELIMS;

    /**
     * @const string ALLOWED_CHARS_IN_PATH Allowed characters in path URI component for use in a regex
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.3
     */
    private const ALLOWED_CHARS_IN_PATH = self::UNRESERVED_CHARS . self::SUB_DELIMS . ':@\/';

    /**
     * @const string ALLOWED_CHARS_IN_QUERY Allowed characters in query URI component for use in a regex
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.4
     */
    private const ALLOWED_CHARS_IN_QUERY = self::UNRESERVED_CHARS . self::SUB_DELIMS . ':@\/\?';

    /**
     * @const string ALLOWED_CHARS_IN_FRAGMENT Allowed characters in fragment URI component for use in a regex
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.5
     */
    private const ALLOWED_CHARS_IN_FRAGMENT = self::UNRESERVED_CHARS . self::SUB_DELIMS . ':@\/\?';

    /**
     * @const int[] DEFAULT_PORTS Default ports by scheme
     */
    public const DEFAULT_PORTS = [
        'ftp'    => 21,
        'gopher' => 70,
        'http'   => 80,
        'https'  => 443,
        'imap'   => 143,
        'ldap'   => 389,
        'nntp'   => 119,
        'news'   => 119,
        'pop'    => 110,
        'telnet' => 23,
        'tn3270' => 23
    ];

    /**
     * @const int MAX_PORT_NUMBER Maximum TCP and UDP port number
     *
     * @see https://en.wikipedia.org/wiki/List_of_TCP_and_UDP_port_numbers
     */
    private const MAX_PORT_NUMBER = 65535;

    // =================================================================================================================
    //
    //      ATTRIBUTES
    //
    // =================================================================================================================

    /**
     * @var string $scheme Scheme URI component
     */
    private $scheme = '';

    /**
     * @var string $host Host URI component
     */
    private $host = '';

    /**
     * @var int|null $port Port URI component
     */
    private $port;

    /**
     * @var string $user User URI component
     */
    private $user = '';

    /**
     * @var string|null $pass Password URI component
     */
    private $pass;

    /**
     * @var string $path Path URI component
     */
    private $path = '';

    /**
     * @var string $query Query URI component
     */
    private $query = '';

    /**
     * @var string $fragment Fragment URI component
     */
    private $fragment = '';

    // =================================================================================================================
    //
    //      METHODS
    //
    // =================================================================================================================

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > GENERAL URI
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Constructor
     *
     * @param string $uri URI in string format
     *
     * @throws InvalidArgumentException On malformed URI provided
     */
    public function __construct(string $uri = '')
    {
        if ($uri !== '') {
            if (($parsedUri = parse_url($uri)) === false) {
                throw new InvalidArgumentException('Unable to parse URI: "' . $uri . '".');
            }
            foreach ($parsedUri as $component => $value) {
                $method = 'set' . ucfirst($component);
                if (method_exists($this, $method)) {
                    $this->$method($value);
                }
            }
        }
    }

    /**
     * @return Uri HTTP URI from globals
     */
    public static function fromGlobals() : Uri
    {
        $uri = (new Uri())->withScheme(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http');

        if (isset($_SERVER['HTTP_HOST'])) {
            $hostUri = new Uri('//' . $_SERVER['HTTP_HOST']);
            $uri = $uri->withHost($hostUri->gethost())->withPort($hostUri->getPort());
        } else {
            if (isset($_SERVER['SERVER_NAME'])) {
                $uri = $uri->withHost($_SERVER['SERVER_NAME']);
            } elseif (isset($_SERVER['SERVER_ADDR'])) {
                $uri = $uri->withHost($_SERVER['SERVER_ADDR']);
            }
            if (isset($_SERVER['SERVER_PORT'])) {
                $uri = $uri->withPort($_SERVER['SERVER_PORT']);
            }
        }
        if (isset($_SERVER['REQUEST_URI'])) {
            $parts = explode('?', $_SERVER['REQUEST_URI'], 2);
            $uri = $uri->withPath($parts[0]);
            if (isset($parts[1])) {
                $uri = $uri->withQuery($parts[1]);
            }
        }
        if ($uri->getQuery() === '' && isset($_SERVER['QUERY_STRING'])) {
            $uri = $uri->withQuery($_SERVER['QUERY_STRING']);
        }
        return $uri;
    }

    /**
     * Returns URI string value
     *
     * @return string URI in string format
     */
    public function __toString() : string
    {
        $uri = $this->getScheme() ? $this->getScheme() . ':' : '';
        if ($this->getAuthority()) {
            $uri .= '//' . $this->getAuthority();
        }
        if ($this->getPath() !== '') {
            if ($this->getPath()[0] != '/' && $this->getAuthority()) {
                $uri .= '/' . $this->getPath();
            } else {
                $uri .= $this->getPath();
            }
        }
        if ($this->getQuery()) {
            $uri .= '?' . $this->getQuery();
        }
        if ($this->getFragment()) {
            $uri .= '#' . $this->getFragment();
        }
        return $uri;
    }

    /**
     * Returns whether or not URI is a relative reference.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-4.1
     *
     * @return bool
     */
    public function isRelativeReference() : bool
    {
        return $this->getScheme() === '';
    }

    /**
     * Percent-encode URI component
     *
     * @param string $component    URI component to percent-encode
     * @param string $allowedChars Allowed characters for use in a regex
     *
     * @return string Percent-encoded URI component
     */
    private function encodeComponent(string $component, string $allowedChars) : string
    {
        return preg_replace_callback(
            '/(?:[^' . $allowedChars . '%]+|%(?![a-fA-F0-9]{2}))/',
            function ($matches) {
                return rawurlencode($matches[0]);
            },
            $component
        );
    }

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
    public function getScheme() : string
    {
        return $this->scheme;
    }

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
    public function withScheme(string $scheme = '') : UriInterface
    {
        $uri = clone $this;
        $uri->setScheme($scheme);
        return $uri;
    }

    /**
     * Sets scheme URI component
     *
     * @param string $scheme Scheme URI component
     *
     * @return void
     */
    private function setScheme(string $scheme) : void
    {
        if ($scheme === '') {
            $this->scheme = '';
            return;
        }
        $scheme = strtolower($scheme);
        if (!preg_match(self::SCHEME_PATTERN, $scheme)) {
            throw new InvalidArgumentException('Invalid URI sheme: "' . $scheme . '"');
        }
        if ($scheme !== 'http' && $scheme !== 'https') {
            throw new InvalidArgumentException('Unsupported URI sheme: "' . $scheme . '"');
        }
        $this->scheme = $scheme;
    }

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
    public function getAuthority() : string
    {
        $authority = '';
        if ($this->getUserInfo() !== '') {
            $authority .= $this->getUserInfo() . '@';
        }
        if ($this->getHost() !== '') {
            $authority .= $this->getHost();
        }
        if ($this->getPort() !== null) {
            $authority .= ':' . $this->getPort();
        }
        return $authority;
    }

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
    public function getUserInfo() : string
    {
        $info = $this->user;
        if ($this->pass) {
            $info .= ':' . $this->pass;
        }
        return $info;
    }

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
     * @return UriInterface A ststic instance with the specified user information.
     */
    public function withUserInfo(string $user, ?string $password = null) : UriInterface
    {
        $uri = clone $this;
        if ($user == '') {
            $uri->user = '';
            $uri->pass = null;
        } else {
            $uri->setUser($user);
            if ($password !== null) {
                $uri->setPass($password);
            }
        }
        return $uri;
    }

    /**
     * Sets user URI component
     *
     * @param string $user User URI component
     * 
     * @throws InvalidArgumentException If no host is set
     *
     * @return void
     */
    private function setUser(string $user) : void
    {
        if ($this->getHost() === '') {
            throw new InvalidArgumentException('Cannot apply UserInfo on URI without host.');
        }
        $this->user = $this->encodeUserInfo($user);
    }

    /**
     * Sets password URI component
     *
     * @param string $pass Password URI component
     *
     * @return void
     */
    private function setPass(string $pass) : void
    {
        $this->pass = $this->encodeUserInfo($pass);
    }

    /**
     * Percent-encodes user name or password
     *
     * @param string $userInfo User information to percent-ecode
     *
     * @return string Percent-encoded user information
     */
    private function encodeUserInfo(string $userInfo) : string
    {
        return $userInfo === ''
            ? ''
            : $this->encodeComponent($userInfo, self::ALLOWED_CHARS_IN_USERINFO);
    }

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
    public function getHost() : string
    {
        return $this->host;
    }

    /**
     * Return an instance with the specified host.
     *
     * An empty host value is equivalent to removing the host.
     *
     * @param string $host The hostname to use with the new instance.
     *
     * @throws InvalidArgumentException for invalid hostnames.
     *
     * @return UriInterface A static instance with specified host
     */
    public function withHost(string $host) : UriInterface
    {
        $uri = clone $this;
        $uri->setHost($host);
        return $uri;
    }

    /**
     * Sets host URI component
     *
     * @param string $host Host URI component
     * 
     * @throws InvalidArgumentException On invalid host name
     *
     * @return void
     */
    private function setHost(string $host) : void
    {
        if ($host !== '') {
            $host = strtolower($host);
            if (!preg_match(
                '`^(([a-z0-9]|[a-z0-9][a-z0-9\-]*[a-z0-9])\.)*([a-z0-9]|[a-z0-9][a-z0-9\-]*[a-z0-9])$`',
                $host
            )) {
                throw new InvalidArgumentException('Invalid host name provided.');
            }
        }
        $this->host = $host;
    }

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
    public function getPort() : ?int
    {
        return $this->port;
    }

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
    public function withPort(?int $port) : UriInterface
    {
        $uri = clone $this;
        $uri->setPort($port);
        return $uri;
    }

    /**
     * Sets port URI component
     *
     * @see https://en.wikipedia.org/wiki/List_of_TCP_and_UDP_port_numbers
     *
     * @param int|null $port Port URI component
     *
     * @throws InvalidArgumentException for invalid ports (outside the established TCP and UDP port ranges).
     *
     * @return void
     */
    private function setPort(?int $port) : void
    {
        if ($port === null) {
            $this->port = null;
            return;
        }
        if ($this->getHost() === '') {
            throw new InvalidArgumentException('URI without Host cannot contain port.');
        }
        if ($port < 0 || $port > self::MAX_PORT_NUMBER) {
            throw new InvalidArgumentException(
                'Invalid port number: provided port is outside established TCP and UDP port ranges.'
            );
        }
        if ($this->getScheme() !== ''
            && isset(self::DEFAULT_PORTS[$this->getScheme()])
            && self::DEFAULT_PORTS[$this->getScheme()] == $port
        ) {
            $this->port = null;
            return;
        }
        $this->port = $port;
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > PATH COMPONENT
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.3
     *
     * @return string The path URI component (can be empty)
     */
    public function getPath() : string
    {
        return $this->path;
    }

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
    public function withPath(string $path) : UriInterface
    {
        $uri = clone $this;
        $uri->setPath($path);
        return $uri;
    }

    /**
     * Sets Path URI component
     *
     * @param string $path Path URI component (can be empty)
     *
     * @throws InvalidArgumentException On Invalid path
     *
     * @return void
     */
    private function setPath(string $path) : void
    {
        if ($this->getAuthority() === '' && strpos($path, '//') === 0) {
            throw new InvalidArgumentException(
                'Cannot apply a path starting with "//" on an URI not containing authority.'
            );
        } elseif ($this->getAuthority() !== '' && $path !== '' && strpos($path, "/") !== 0) {
            throw new InvalidArgumentException(
                'Cannot apply rootless path on an URI containing authority.'
            );
        }
        $this->path = $path === ''
            ? ''
            : $this->encodeComponent($path, self::ALLOWED_CHARS_IN_PATH);
    }

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
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * Returns an instance with the specified query string.
     * 
     * @param string $query The query string to use with the new instance.
     * Users can provide both encoded and decoded query characters.
     * An empty query string value is equivalent to removing the query string.
     *
     * @return UriInterface A static instance with the specified query string.
     */
    public function withQuery(string $query) : UriInterface
    {
        $uri = clone $this;
        $uri->setQuery($query);
        return $uri;
    }

    /**
     * Parses query string
     * 
     * @param string $query Query string component (The leading "?" must not be added)
     * 
     * @return array An associative array of query vars
     */
    public static function parseQuery(string $query) : array
    {
        $parsed = [];

        if ($query !== '') {
            foreach (explode('&', $query) as $pair) {
                $pair = explode('=', $pair);

                if (!isset($parsed[$pair[0]])) {
                    $parsed[$pair[0]] = $pair[1] ?? '';
                } elseif(is_array($parsed[$pair[0]])) {
                    $parsed[$pair[0]][] = $pair[1] ?? '';
                } else {
                    $parsed[$pair[0]] = [$parsed[$pair[0]], $pair[1] ?? ''];
                }
            }
        }
        return $parsed;
    }

    /**
     * Builds query string from provided associative array
     * 
     * @param array $params An associative array
     * 
     * @return string The builded query string
     */
    public static function buildQuery(array $params) : string
    {
        $query = '';
        foreach ($params as $key => $value) {
            $query .= '&' . $key . '=' . strval($value);
        }
        return ltrim($query, '&');
    }

    /**
     * Sets query URI component
     *
     * @param string $query Query URI component
     *
     * @return void
     */
    private function setQuery(string $query) : void
    {
        $this->query = $query === ''
            ? ''
            : $this->encodeComponent($query, self::ALLOWED_CHARS_IN_QUERY);
    }

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
    public function getFragment() : string
    {
        return $this->fragment;
    }

    /**
     * Returns an instance with the specified URI fragment.
     *
     * @param string $fragment The fragment to use with the new instance.
     * Users can provide both encoded and decoded fragment characters.
     * An empty fragment value is equivalent to removing the fragment.
     *
     * @return UriInterface A static instance with the specified fragment.
     */
    public function withFragment(string $fragment) : UriInterface
    {
        $uri = clone $this;
        $uri->setFragment($fragment);
        return $uri;
    }

    /**
     * Sets fragment URI component
     *
     * @param string $fragment Fragment URI component
     *
     * @return void
     */
    private function setFragment(string $fragment) : void
    {
        if ($fragment !== '') {
            $this->fragment = $this->encodeComponent($fragment, self::ALLOWED_CHARS_IN_FRAGMENT);
        } else {
            $this->fragment = '';
        }
    }
}
