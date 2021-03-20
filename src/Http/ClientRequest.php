<?php

namespace Chiphpmunk\Http;

use InvalidArgumentException;
use RuntimeException;

class ClientRequest extends Request
{
    /**
     * @var string|null $certificate SSL certificate
     */
    private $certificate;

    /**
     * @var resource|null $session cURL session
     */
    private $session;

    /**
     * Constructor
     * 
     * @param UriInterface $uri         Client URI
     * @param string|null  $certificate SSL certificate file if it is necessary.
     * 
     * @throws InvalidArgumentException On invalid certificate file
     */
    public function __construct(UriInterface $uri, ?string $certificate = null)
    {
        $this->uri = $uri;
        
        if ($certificate !== null) {
            if (is_file($certificate)) {
                $this->certificate = $certificate;
            } else {
                throw new InvalidArgumentException('Provided argument is not a valid file: "' . $certificate . '".');
            }
        }
    }

    /**
     * Destructor
     * 
     * Closes cURL session
     */
    public function __destruct()
    {
        $this->closeSession();
    }

    /**
     * Processes the request to produce a response.
     * 
     * @throws RuntimeException On any error
     * 
     * @return Response HTTP response
     */
    public function process() : Response
    {
        $this->closeSession();
        if (($this->session = curl_init((string) $this->uri)) === false) {
            $this->session = null;
            throw new RuntimeException('Could not init cURL session with provided URI: "' . (string) $this->uri . '".');
        }
        if ($this->certificate !== null && !curl_setopt($this->session, CURLOPT_CAINFO, $this->certificate)) {
            throw new RuntimeException('An error occured with provided certificate: ' . curl_error($this->session));
        }

        if (!curl_setopt_array($this->session, $this->buildOptions())
            || ($responseBody = curl_exec($this->session)) === false
        ) {
            throw new RuntimeException('An error occured sending the client request: ' . curl_error($this->session));
        }

        $response = (new Response())
            ->withBody($responseBody)
            ->withStatus(curl_getinfo($this->session, CURLINFO_HTTP_CODE));
        
        if (($type = curl_getinfo(CURLINFO_CONTENT_TYPE)) !== null) {
            $response = $response->withHeader('Content-type', $type);
        }
        return $response;
    }

    /**
     * Closes cURL session
     * 
     * @return void
     */
    private function closeSession() : void
    {
        if ($this->session !== null) {
            curl_close($this->session);
            $this->session = null;
        }
    }

    /**
     * Builds cURL options array
     * 
     * @return mixed[] cURL options array
     */
    private function buildOptions() : array
    {
        return array_merge(
            $this->buildProtocolOptions(),
            $this->buildMethodOptions(),
            [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER         => true
            ]
        );
    }

    /**
     * Builds protocol version options
     * 
     * @return int[]
     */
    private function buildProtocolOptions() : array
    {

        $options = [
            CURLOPT_DEFAULT_PROTOCOL => 'http',
            CURLOPT_ENCODING         => 'utf-8',
            CURLOPT_PORT             => 80
        ];

        switch ($this->getProtocolVersion()) {
            case '1.0':
                $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_0;
            break;
            case '1.1':
                $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
            break;
            case '2':
                $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_2;
            break;
            case '2.0':
                $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_2_0;
            break;
            default: break;
        }
        return $options;
    }

    /**
     * Builds method options
     * 
     * @return string[] 
     */
    private function buildMethodOptions() : array
    {
        $options = [];

        if ($method = strtolower($this->getMethod()) !== 'get') {
            switch ($method) {
                case 'post':
                    $options[CURLOPT_POST] = true;
                break;
                case 'head':
                    $options[CURLOPT_NOBODY] = true;
                break;
                case 'put':
                    $options[CURLOPT_PUT] = true;
                break;
                default: break;
            }
            if ($method !== 'head') {
                $options[CURLOPT_CUSTOMREQUEST] = $this->getMethod();
            }
        }
        return $options;
    }
}
