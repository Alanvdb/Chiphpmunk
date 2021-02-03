<?php

use PHPUnit\Framework\TestCase;

use Chiphpmunk\Http\ResponseInterface;
use Chiphpmunk\Http\Response;

class ResponseTest extends TestCase
{
    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / __construct()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Response must be instanciable without argument and must implement ResponseInterface.
     */
    public function test_construct_withoutArgument()
    {
        $response = new Response();
        $this->assertInstanceOf(
            ResponseInterface::class,
            $response,
            'Response must be instanciable without argument.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / getStatusCode()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * getStatusCode() must return integer value.
     */
    public function test_getStatusCode_returnInteger()
    {
        $this->assertIsInt(
            (new Response())->getStatusCode(),
            'getStatusCode() must return integer value.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / getReasonPhrase()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * getReasonPhrase() must return string value.
     */
    public function test_getReasonPhrase_returnString()
    {
        $this->assertIsString(
            (new Response())->getReasonPhrase(),
            'getReasonPhrase() Must return string value.'
        );
    }

    /**
     * getReasonPhrase() must return default phrase for code provided via withStatus() if no phrase was provided.
     */
    public function test_getReasonPhrase_defaultPhrase()
    {
        $response = (new Response())->withStatus(404);
        $this->assertSame(
            'Not Found',
            $response->getReasonPhrase(),
            'getReasonPhrase() must return default phrase for code provided via withStatus() if no phrase was provided.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / withStatus()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * withStatus() must work with the first argument only and return provided code via getStatusCode().
     * 
     * @depends test_getStatusCode_returnInteger
     */
    public function test_withStatus_withOnlyTheFirstArgument()
    {
        $response = (new Response())->withStatus(404);
        $this->assertSame(
            404,
            $response->getStatusCode(),
            'withStatus() must work with the first argument only and return provided code via getStatusCode().'
        );
    }

    /**
     * getReasonPhrase() must return the phrase provided via withStatus().
     * 
     * @depends test_getReasonPhrase_defaultPhrase
     */
    public function test_withStatus_withCustomPhrase()
    {
        $response = (new Response())->withStatus(404, 'test');
        $this->assertSame(
            'test',
            $response->getReasonPhrase(),
            'getReasonPhrase() must return the phrase provided via withStatus().'
        );
    }

    /**
     * withStatus() must throw InvalidArgumentException if provided code is invalid.
     */
    public function test_withStatus_withInvalidCode()
    {
        $this->expectException(InvalidArgumentException::class);
        (new Response())->withStatus(90);
    }
}
