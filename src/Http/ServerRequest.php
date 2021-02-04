<?php declare(strict_types=1);

namespace Chiphpmunk\Http;

use Chiphpmunk\Container;

use InvalidArgumentException;

class ServerRequest extends Request implements ServerRequestInterface
{
    // =================================================================================================================
    //
    //      ATTRIBUTES
    //
    // =================================================================================================================

    /**
     * @var mixed[] $uploadedFiles Uploaded files
     */
    private $uploadedFiles = [];

    /**
     * @var string[] $cookieParams Cookie parameters
     */
    private $cookieParams = [];

    /**
     * @var string[] $queryParams Query parameters
     */
    private $queryParams = [];

    /**
     * @var null|array|object $parsedBody The deserialized body data
     */
    private $parsedBody;

    /**
     * @var mixed[] $attributes An associative array of attributes
     */
    private $attributes = [];

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > GLOBALS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return ServerRequest Incoming HTTP request with attributes derived from PHP globals
     */
    public static function fromGlobals() : ServerRequestInterface
    {
        $request = (new ServerRequest())
            ->withQueryParams($_GET)
            ->withParsedBody($_POST)
            ->withCookieParams($_COOKIE)
            ->withUploadedFiles(self::normalizeUploadedFileArray($_FILES))
            ->withMethod($_SERVER['REQUEST_METHOD'] ?? 'GET')
            ->withUri(Uri::fromGlobals())
            ->withProtocolVersion(
                isset($_SERVER['SERVER_PROTOCOL'])
                    ? str_replace('HTTP/', '', $_SERVER['SERVER_PROTOCOL'])
                    : '1.1'
            );
        
        foreach (getallheaders() as $headerName => $headerValues) {
            $request = $request->withHeader($headerName, explode(',', $headerValues));
        }
        return $request;
    }

    /**
     * @return array Server parameters
     */
    public function getServerParams() : array
    {
        return $_SERVER;
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > QUERY
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Returns query string arguments.
     *
     * Note: the query params might not be in sync with the URI or server
     * params. If you need to ensure you are only getting the original
     * values, you may need to parse the query string from `getUri()->getQuery()`
     * or from the `QUERY_STRING` server param.
     *
     * @return string[]
     */
    public function getQueryParams() : array
    {
        return $this->queryParams;
    }

    /**
     * Returns an instance with the specified query string arguments.
     *
     * @param string[] $queryParams Array of query string arguments, typically from $_GET.
     *
     * @throws InvalidArgumentException On invalid query parameters structure
     *
     * @return ServerRequestInterface
     */
    public function withQueryParams(array $queryParams) : ServerRequestInterface
    {
        $this->assertQueryParams($queryParams);
        $request = clone $this;
        $request->queryParams = $queryParams;
        return $request;
    }

    /**
     * Asserts query parameters
     *
     * @param string[] $queryParams Associative array like ['queryAttribute' => 'queryValue', ...].
     *
     * @throws InvalidArgumentException On invalid query parameters structure
     *
     * @return void
     */
    private function assertQueryParams(array $queryParams)
    {
        foreach ($queryParams as $name => $value) {
            if (!is_string($name)) {
                throw new InvalidArgumentException('Query attribute names must be of type string.');
            }
            if (!is_string($value)) {
                if (!is_array($value) || !$this->assertQueryValue($value)) {
                    throw new InvalidArgumentException('Query attribute values must be of type string or an array where the leaf is a string.');
                }
            }
        }
    }

    /**
     * Asserts query value when query param is a multidimensional array.
     * 
     * @param array $values The query parameter value
     * 
     * @return bool
     */
    private function assertQueryValue(array $values) : bool
    {
        foreach ($values as $value) {
            if (!is_string($value) && (!is_array($value) || !$this->assertQueryValue($value))) {
                return false;
            }
        }
        return true;
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > COOKIE
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Returns cookies.
     *
     * @return string[] Associative array like ['cookieName' => 'cookieValue', ...].
     */
    public function getCookieParams() : array
    {
        return $this->cookieParams;
    }

    /**
     * Returns an instance with the specified cookies.
     *
     * This method MUST NOT update the related Cookie header of the request
     * instance, nor related values in the server params.
     *
     * @param string[] $cookieParams Associative array like ['cookieName' => 'cookieValue', ...].
     *
     * @throws InvalidArgumentException On invalid cookie parameters
     *
     * @return ServerRequestInterface
     */
    public function withCookieParams(array $cookieParams) : ServerRequestInterface
    {
        $this->assertCookieParams($cookieParams);
        $request = clone $this;
        $request->cookieParams = $cookieParams;
        return $request;
    }

    /**
     * Asserts cookie parameters
     *
     * @param string[] $cookieParams Associative array like ['cookieName' => 'cookieValue', ...].
     *
     * @throws InvalidArgumentException On invalid cookie parameters structure
     *
     * @return void
     */
    private function assertCookieParams(array $cookieParams) : void
    {
        foreach ($cookieParams as $cookieName => $cookieValue) {
            if (!is_string($cookieName)) {
                throw new InvalidArgumentException('Cookie names must be string values.');
            }
            if (strpos($cookieName, ' ') !== false || strpos($cookieName, '.') !== false) {
                throw new InvalidArgumentException('Cookie names cannot contains spaces or dot (".") characters.');
            }
            if (!is_string($cookieValue)) {
                throw new InvalidArgumentException('Cookie values must be of type string.');
            }
        }
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > UPLOADED FILES
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Retrieve normalized file upload data.
     *
     * This method returns upload metadata in a normalized tree, with each leaf
     * an instance of UploadedFileInterface.
     *
     * @return array An array tree of UploadedFileInterface instances
     * An empty array is returned if no data is present.
     */
    public function getUploadedFiles() : array
    {
        return $this->uploadedFiles;
    }

    /**
     * Create a new instance with the specified uploaded files.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated body parameters.
     *
     * @param array $uploadedFiles An array tree of UploadedFileInterface instances.
     * 
     * @throws InvalidArgumentException if an invalid structure is provided.
     * 
     * @return ServerRequestInterface An instance with provided uploaded files
     */
    public function withUploadedFiles(array $uploadedFiles) : ServerRequestInterface
    {
        if (!$this->assertUploadedFileArray($uploadedFiles)) {
            throw new InvalidArgumentException('Invalid array structure provided.');
        }
        $request = clone $this;
        $request->uploadedFiles = $uploadedFiles;
        return $request;
    }

    /**
     * Checks Uploaded files array
     */
    private function assertUploadedFileArray(array $uploadedFiles) : bool
    {
        $isValid = true;

        foreach ($uploadedFiles as $file) {
            if (is_array($file)) {
                if (!$this->assertUploadedFileArray($file)) {
                    $isValid = false;
                    break;
                } 
            } elseif (!($file instanceof UploadedFileInterface)) {
                $isValid = false;
                break;
            }
        }
        return $isValid;
    }

    /**
     * @param mixed[] $files An array structured like $_FILES array
     * 
     * @see https://www.php-fig.org/psr/psr-7/
     * 
     * @return array Normalized array with UploadedFile instance(s)
     */
    public static function normalizeUploadedFileArray(array $files) : array
    {
        if (empty($files)) {
            return [];
        }
        $keys = ['error', 'name', 'size', 'tmp_name', 'type'];
        $normalized = [];
        foreach ($files as $inputName => $value) {
            if (!is_array($value['error'])) {
                $normalized[$inputName] = new UploadedFile($value['name'], $value['type'], $value['tmp_name'], $value['error'], $value['size']);;
            } else {
                $subKeys    = self::catchUploadedFileArrayKeys($value['error']);
                $filesData  = [];
                $orderedFileData = [];
                foreach ($keys as $attribute) {
                    $filesData[$attribute] = self::catchUploadedFileAttribute($files[$inputName][$attribute]);
                    if (!isset($count)) {
                        $count = count($filesData[$attribute]);
                    }
                    for ($i = 0; $i < $count; $i++) {
                        $orderedFileData[$i][$attribute] = $filesData[$attribute][$i];
                    }
                }
                $uploadedFileObjects = [];
                foreach ($orderedFileData as $file) {
                    $uploadedFileObjects[] = new UploadedFile($file['name'], $file['type'], $file['tmp_name'], $file['error'], $file['size']);
                } 
                if (count($uploadedFileObjects) > 1) {
                    $normalized[$inputName] = self::bindUploadedFileAttribute($subKeys, $uploadedFileObjects);
                } else {
                    $normalized[$inputName] = self::bindUploadedFileAttribute($subKeys, $uploadedFileObjects[0]);
                }
            }
        }
        return $normalized;
    }

    private static function catchUploadedFileArrayKeys(array $array)
    {
        $keys = array_keys($array);
        if (is_array($array[$keys[0]]) && count($array[$keys[0]]) === 1) {
            $keys = array_merge($keys, self::catchUploadedFileArrayKeys($array[$keys[0]]));
            return $keys;
        }
        return $keys;
    }

    private static function catchUploadedFileAttribute(array $array)
    {
        $keys = array_keys($array);
        if (is_array($array[$keys[0]]) && count($array[$keys[0]]) === 1) {
            return self::catchUploadedFileAttribute($array[$keys[0]]);
        } elseif (is_array($array[$keys[0]])) {
            return $array[$keys[0]];
        } else {
            return [$array[$keys[0]]];
        }
    }

    private static function bindUploadedFileAttribute(array $keys, $value)
    {
        if (!empty($keys)) {
            $key = array_shift($keys);
            return [$key => self::bindUploadedFileAttribute($keys, $value)];
        } else {
            return $value;
        }
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > BODY
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return null|array|object The deserialized body parameters, if any.
     * If the request Content-Type is either application/x-www-form-urlencoded
     * or multipart/form-data, and the request method is POST, this method will
     * return the contents of $_POST.
     */
    public function getParsedBody()
    {
        return $this->parsedBody;
    }

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
    public function withParsedBody($data) : ServerRequestInterface
    {
        if (!is_null($data) && !is_array($data) && !is_object($data)) {
            throw new InvalidArgumentException('Argument must be an array, an object or null.');
        }
        $request = clone $this;
        $request->parsedBody = $data;
        return $request;
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > ATTRIBUTES
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Retrieve a single derived request attribute.
     *
     * Retrieves a single derived request attribute as described in
     * getAttributes(). If the attribute has not been previously set, returns
     * the default value as provided.
     *
     * @see getAttributes()
     *
     * @param string $name    The attribute name.
     * @param mixed  $default Default value to return if the attribute does not exist.
     *
     * @return mixed
     */
    public function getAttribute(string $name, $default = null)
    {
        return array_key_exists($name, $this->attributes) ? $this->attributes[$name] : $default;
    }

    /**
     * Retrieve attributes derived from the request.
     *
     * The request "attributes" may be used to allow injection of any
     * parameters derived from the request: e.g., the results of path
     * match operations; the results of decrypting cookies; the results of
     * deserializing non-form-encoded message bodies; etc. Attributes
     * will be application and request specific, and CAN be mutable.
     *
     * @return mixed[] Attributes derived from the request.
     */
    public function getAttributes() : array
    {
        return $this->attributes;
    }

    /**
     * Returns the request with provided attribute
     *
     * @param string $name  Attribute name.
     * @param mixed  $value The value of the attribute.
     * 
     * @throws InvalidArgumentException if provided name is empty
     * 
     * @return self The request with provided attribute
     */
    public function withAttribute(string $name, $value) : ServerRequestInterface
    {
        if (array_key_exists($name, $this->attributes) && $this->attributes[$name] === $value) {
            return $this;
        }
        $clone = clone $this;
        $clone->attributes[$name] = $value;
        return $clone;
    }

    /**
     * Returns the request without the specified attribute.
     *
     * @param string $name The attribute name
     * 
     * @return self The request without specified attribute
     */
    public function withoutAttribute(string $name) : ServerRequestInterface
    {
        if (!array_key_exists($name, $this->attributes)) {
            return $this;
        }
        $clone = clone $this;
        unset($clone->attributes[$name]);
        return $clone;
    }
}
