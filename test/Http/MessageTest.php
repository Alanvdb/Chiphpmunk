<?php

use PHPUnit\Framework\TestCase;

use Chiphpmunk\Http\MessageInterface;
use Chiphpmunk\Http\MessageTrait;

/**
 * Class used to test Message
 */
class Message implements MessageInterface
{
    use MessageTrait;
}

/**
 * Test class
 */
class MessageTest extends TestCase
{
    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / getProtocolVersion()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * getProtocolVersion() must return a string value.
     */
    public function test_getProtocolVersion_returnsString()
    {
        $message = new Message();
        $this->assertIsString(
            $message->getProtocolVersion(),
            'getProtocolVersion() must return a string value.'
        );
    }

    /**
     * getProtocolVersion() must return a default version number.
     * 
     * @depends test_getProtocolVersion_returnsString
     */
    public function test_getProtocolVersion_returnsDefaultVersionNumber()
    {
        $message = new Message();
        $this->assertMatchesRegularExpression(
            '`^[0-9]+\.[0-9]+$`',
            $message->getProtocolVersion(),
            'getProtocolVersion() must return a default version number.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / withProtocolVersion()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * withProtocolVersion() must return provided number via getProtocolVersion().
     * 
     * @depends test_getProtocolVersion_returnsString
     */
    public function test_withProtocolVersion_returnsProvidedNumber()
    {
        $message = (new Message())->withProtocolVersion('2.0');
        $this->assertSame(
            '2.0',
            $message->getProtocolVersion(),
            'withProtocolVersion() must return provided number via getProtocolVersion().'
        );
    }

    /**
     * withProtocolVersion() must retain the state of the current instance.
     */
    public function test_withProtocolVersion_returnsNewInstance()
    {
        $message = (new Message())->withProtocolVersion('2.0');
        $message2 = $message->withProtocolVersion('1.0');

        $this->assertNotSame(
            $message,
            $message2,
            'withProtocolVersion() must retain the state of the current instance.'
        );
    }

    /**
     * withProtocolVersion() must retain the version number only.
     */
    public function test_withProtocolVersion_keepVersionNumberOnly()
    {
        $message = (new Message())->withProtocolVersion('HTTP/1.0');
        $this->assertSame(
            '1.0',
            $message->getProtocolVersion(),
            'withProtocolVersion() must retain the version number only.'
        );
    }

    /**
     * withProtocolVersion() must throw InvalidArgumentException on invalid version number.
     */
    public function test_withProtocolVersion_withInvalidArgument()
    {
        $this->expectException(InvalidArgumentException::class);
        $message = (new Message())->withProtocolVersion('yaiuzygdiauyzb');
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / hasHeader()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * A message without header must return false via hasHeader() method.
     */
    public function test_hasHeader_withoutHeader()
    {
        $message = new Message();
        $this->assertFalse(
            $message->hasHeader('host'),
            'A message without header must return false via hasHeader() method.'
        );
    }

    /**
     * Provided argument for hasHeader() must be case-insensitive.
     */
    public function test_hasHeader_isCaseInsensitive()
    {
        $message = (new Message())->withHeader('HoSt', 'localhost');
        $this->assertTrue(
            $message->hasHeader('hOsT'),
            'Provided argument for hasHeader() must be case-insensitive.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / getHeaders()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * getHeaders() must return headers specified via withHeader() method.
     */
    public function test_getHeaders_returnsHeadersProvidedViaWithHeaderMethod()
    {
        $message = (new Message())
            ->withHeader('Host', 'localhost')
            ->withHeader('LocatioN', '/test');

        $this->assertEquals(
            ['Host' => [0 => 'localhost'], 'LocatioN' => [0 => '/test']],
            $message->getHeaders(),
            'getHeaders() must return headers specified via withHeader() method.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / getHeader()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * getHeader() must return provided header via withHeader() method.
     */
    public function test_getHeader_returnsProvidedHeaderViaWithHeaderMethod()
    {
        $message = (new Message())->withHeader('Host', 'localhost');
        $this->assertContains(
            'localhost',
            $message->getHeader('Host'),
            'getHeader() must return provided header via withHeader() method.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / getHeaderLine()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * getHeaderLine() must return coma separated values of the specified header.
     * 
     * @depends test_withAddedHeader_appendsNewValue
     */
    public function test_getHeaderLine_returnsComaSeparatedLine()
    {
        $message = (new Message())
            ->withHeader('foo', 'bar')
            ->withAddedHeader('foo', 'baz');

        $this->assertEquals(
            'bar,baz',
            $message->getHeaderLine('foo'),
            'getHeaderLine() must return coma separated values of the specified header.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / withHeader()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * withHeader() must retain the state of the previous message.
     */
    public function test_withHeader_returnsNewInstance()
    {
        $message = new Message();
        $message2 = $message->withHeader('Host', 'localhost');

        $this->assertNotSame(
            $message,
            $message2,
            'withHeader() must retain the state of the previous message.'
        );
    }

    /**
     * withHeader() must override previous header.
     */
    public function test_withHeader_override()
    {
        $message = (new Message())->withHeader('Host', 'localhost');
        $message = $message->withHeader('host', 'test.com');
        $this->assertNotContains(
            'localhost',
            $message->getHeader('Host'),
            'withHeader() must override previous header.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / withAddedHeader()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * hasHeader() must retrieve header in a case-insensitive manner when header is added via withAddedHeader method.
     */
    public function testWithAddedHeaderIsCaseInsensitive()
    {
        $message = (new Message())->withAddedHeader('fOo', 'baz');
        $this->assertTrue(
            $message->hasHeader('FOO'),
            'hasHeader() must retrieve header in a case-insensitive manner when header is added via withAddedHeader method.'
        );
    }

    /**
     * withAddedHeader() method must append header value if specified header already exists.
     */
    public function test_withAddedHeader_appendsNewValue()
    {
        $message = (new Message())->withHeader('foo', 'bar')->withAddedHeader('foo', 'baz');
        $this->assertEquals(
            ['bar', 'baz'],
            $message->getHeader('foo'),
            'withAddedHeader() method must append header value if specified header already exists.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / withoutHeader()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * withoutHeader() method must delete specified header.
     */
    public function test_withoutHeader_deleteHeader()
    {
        $message = (new Message())->withHeader('Host', 'localhost');
        $message = $message->withoutHeader('Host');
        $this->assertFalse(
            $message->hasHeader('Host'),
            'withoutHeader() method must delete specified header.'
        );
    }

    /**
     * withoutHeader() method must delete specified header in a case-insensitive manner.
     * 
     * @depends test_withoutHeader_deleteHeader
     */
    public function test_withoutHeader_IsCaseInsensitive()
    {
        $message = (new Message())->withHeader('Host', 'localhost');
        $message = $message->withoutHeader('HOST');
        $this->assertFalse(
            $message->hasHeader('Host'),
            'withoutHeader() method must delete specified header in a case-insensitive manner.'
        );
    }

    /**
     * withoutHeader() must retain the state of the current message.
     */
    public function test_withoutHeader_returnsNewInstance()
    {
        $message = (new Message())->withHeader('Host', 'localhost');
        $message2 = $message->withoutHeader('Host');
        $this->assertNotSame(
            $message,
            $message2,
            'withoutHeader() must retain the state of the current message.'
        );
    }
}
