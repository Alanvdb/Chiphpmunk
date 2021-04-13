<?php

namespace Chiphpmunk\Http;

use Chiphpmunk\Stream\Stream;

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
        if ($certificate !== null) {
            if (!is_file($certificate)) {
                throw new InvalidArgumentException('Provided argument is not a valid file: "' . $certificate . '".');
            }
            $this->certificate = $certificate;
        }
        $this->uri = $uri;
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
            ->withBody(new Stream($responseBody))
            ->withStatus(curl_getinfo($this->session, CURLINFO_HTTP_CODE));
        
        if (($type = curl_getinfo($this->session, CURLINFO_CONTENT_TYPE)) !== null) {
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
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true
        ];
        $this->catchProtocolOption($options);
        $this->catchMethodOption($options);
        return $options;
    }

    /**
     * Adds protocol option to provided array
     * 
     * @param mixed[] &$options
     * 
     * @return void
     */
    private function catchProtocolOption(array &$options) : void
    {
        $protocolOption = array_search(
            $this->getProtocolVersion(),
            [
                CURL_HTTP_VERSION_1_0 => '1.0',
                CURL_HTTP_VERSION_1_1 => '1.1',
                CURL_HTTP_VERSION_2   => '2',
                CURL_HTTP_VERSION_2_0 => '2.0'
            ]
        );

        if ($protocolOption !== false) {
            $options[CURLOPT_HTTP_VERSION] = $protocolOption;
        }
    }

    /**
     * adds method options to provided array
     * 
     * @param mixed[] &$options
     * 
     * @return void
     */
    private function catchMethodOption(array &$options) : void
    {
        if (($method = strtolower($this->getMethod())) !== 'get') {
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
                default: 
                    $options[CURLOPT_CUSTOMREQUEST] = $this->getMethod();
                break;
            }
        }
    }
}
