<?php

use PHPUnit\Framework\TestCase;

use Chiphpmunk\Stream\Stream;

class StreamTest extends Testcase
{
    /**
     * @afterClass
     */
    public static function clean()
    {
        if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'test.txt')) {
            unlink(__DIR__ . DIRECTORY_SEPARATOR . 'test.txt');
        }
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / __construct()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * __construct() must handle a provided resource.
     */
    public function test_construct_withResource()
    {
        $stream = new Stream(fopen(__DIR__ . DIRECTORY_SEPARATOR . 'test.txt', 'w+'));
        $this->assertInstanceOf(
            Stream::class,
            $stream,
            '__construct() must handle a provided resource.'
        );
    }

    /**
     * __construct() must handle string.
     */
    public function test_construct_withString()
    {
        $stream = new Stream('Hello World !');
        $this->assertInstanceOf(
            Stream::class,
            $stream,
            '__construct() must handle string.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / getSize()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * getSize() must return size from a resource built with fopen() function.
     * 
     * @depends test_construct_withResource
     * 
     * @todo depends on write()
     */
    public function test_getSize_withFopenedResource()
    {
        $stream = new Stream(fopen(__DIR__ . DIRECTORY_SEPARATOR . 'test.txt', 'w'));
        $stream->write('hello');
        $this->assertEquals(
            5,
            $stream->getSize(),
            'getSize() must return size from a resource built with fopen() function.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / getMetadata()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Stream instanciated with a ressource must return resource URI via getMetadata()['uri'].
     * 
     * @depends test_construct_withResource
     */
    public function test_getMetadata_uri()
    {
        $stream = new Stream(fopen(__DIR__ . DIRECTORY_SEPARATOR . 'test.txt', 'r'));
        $this->assertSame(
            __DIR__ . DIRECTORY_SEPARATOR . 'test.txt',
            $stream->getMetadata()['uri'],
            'Stream instanciated with a ressource must return resource URI via getMetadata()[\'uri\'].'
        );
    }

    /**
     * Stream instanciated with a ressource opened in 'r' mode must contain ['mode' => 'r'] via getMetadata() method.
     * 
     * @depends test_construct_withResource
     */
    public function test_getMetadata_mode_withRMode()
    {
        $stream = new Stream(fopen(__DIR__ . DIRECTORY_SEPARATOR . 'test.txt', 'r'));
        $this->assertSame(
            'r',
            $stream->getMetadata()['mode'],
            'Stream instanciated with a ressource opened in \'r\' mode must contain [\'mode\' => \'r\'] via getMetadata() method.'
        );
    }

    /**
     * Stream instanciated with a ressource opened in 'r+' mode must contain ['mode' => 'r+'] via getMetadata() method.
     * 
     * @depends test_construct_withResource
     */
    public function test_getMetadata_mode_withRPlusMode()
    {
        $stream = new Stream(fopen(__DIR__ . DIRECTORY_SEPARATOR . 'test.txt', 'r+'));
        $this->assertSame(
            'r+',
            $stream->getMetadata()['mode'],
            'Stream instanciated with a ressource opened in \'r+\' mode must contain [\'mode\' => \'r+\'] via getMetadata() method.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / isSeekable()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Stream instanciated with a resource opened with fopen() must return true via isSeekable() method.
     * 
     * @depends test_construct_withResource
     */
    public function test_isSeekable_withRMode()
    {
        $stream = new Stream(fopen(__DIR__ . DIRECTORY_SEPARATOR . 'test.txt', 'r'));
        $this->assertTrue(
            $stream->isSeekable(),
            'Stream instanciated with a resource fopen() in "r" mode must return true via isSeekable() method.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / seek()
    // -----------------------------------------------------------------------------------------------------------------

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / rewind()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * rewind() method must place pointer at offset 0.
     * 
     * @depends test_construct_withResource
     * @depends test_tell_withNewStream
     * 
     * @todo depends on write() method
     */
    public function test_rewind()
    {
        $stream = new Stream(fopen(__DIR__ . DIRECTORY_SEPARATOR . 'test.txt', 'w'));
        $stream->write('hello');
        $stream->rewind();
        $this->assertEquals(
            0,
            $stream->tell(),
            'rewind() method must place pointer at offset 0.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / tell()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * tell() method must return 0 from a new empty stream.
     * 
     * @depends test_construct_withResource
     */
    public function test_tell_withNewStream()
    {
        $stream = new Stream(fopen(__DIR__ . DIRECTORY_SEPARATOR . 'test.txt', 'w'));
        $this->assertEquals(
            0,
            $stream->tell(),
            'tell() method must return 0 from a new empty stream.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / eof()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * eof() method must return true when pointer is at the end of the stream.
     * 
     * @depends test_construct_withResource
     * @depends test_rewind
     * 
     * @todo depends on write() and read() method
     */
    public function test_eof()
    {
        $stream = new Stream(fopen(__DIR__ . DIRECTORY_SEPARATOR . 'test.txt', 'w+'));
        $stream->write('hello');
        $stream->rewind();
        $stream->read(6);
        $this->assertTrue(
            $stream->eof(),
            'eof() method must return true when pointer is at the end of the stream.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / isReadable()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Stream instanciated with a ressource opened in 'r' mode must return true via isReadable() method.
     * 
     * @depends test_construct_withResource
     */
    public function test_isReadable_withRMode()
    {
        $stream = new Stream(fopen(__DIR__ . DIRECTORY_SEPARATOR . 'test.txt', 'r'));
        $this->assertTrue(
            $stream->isReadable(),
            'Stream instanciated with a ressource opened in "r" mode must return true via isReadable() method.'
        );
    }

    /**
     * Stream instanciated with a ressource opened in 'r+' mode must return true via isReadable() method.
     * 
     * @depends test_construct_withResource
     */
    public function test_isReadable_withRPlusMode()
    {
        $stream = new Stream(fopen(__DIR__ . DIRECTORY_SEPARATOR . 'test.txt', 'r+'));
        $this->assertTrue(
            $stream->isReadable(),
            'Stream instanciated with a ressource opened in \'r+\' mode must return true via isReadable() method.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / read()
    // -----------------------------------------------------------------------------------------------------------------

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / getContents()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * getContents() method must return remaining contents from pointer position.
     * 
     * @depends test_construct_withResource
     * 
     * @todo depends on write() and seek() method
     */
    public function test_getContents()
    {
        $stream = new Stream(fopen(__DIR__ . DIRECTORY_SEPARATOR . 'test.txt', 'w+'));
        $stream->write('hello');
        $stream->seek(1);
        $this->assertSame(
            'ello',
            $stream->getContents(),
            'getContents() method must return remaining contents from pointer position.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / __toString()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * __toString() method must return what is written.
     * 
     * @depends test_construct_withResource
     * 
     * @todo depends on write() method
     */
    public function test_toString_withresource()
    {
        $stream = new Stream(fopen(__DIR__ . DIRECTORY_SEPARATOR . 'test.txt', 'w+'));
        $stream->write('Hello World !');
        $this->assertEquals(
            'Hello World !',
            (string) $stream,
            '__toString() method must return what is written.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / isWritable()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Stream instanciated with a ressource opened in 'r' mode must return false via isWritable() method.
     * 
     * @depends test_construct_withResource
     */
    public function test_isWritable_withRMode()
    {
        $stream = new Stream(fopen(__DIR__ . DIRECTORY_SEPARATOR . 'test.txt', 'r'));
        $this->assertFalse(
            $stream->isWritable(),
            'Stream instanciated with a ressource opened in "r" mode must return false via isWritable() method.'
        );
    }

    /**
     * Stream instanciated with a ressource opened in 'r+' mode must return true via isWritable() method.
     * 
     * @depends test_construct_withResource
     */
    public function test_isWritable_withRPlusMode()
    {
        $stream = new Stream(fopen(__DIR__ . DIRECTORY_SEPARATOR . 'test.txt', 'r+'));
        $this->assertTrue(
            $stream->isWritable(),
            'Stream instanciated with a ressource opened in \'r+\' mode must return true via isWritable() method.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / write()
    // -----------------------------------------------------------------------------------------------------------------

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / close()
    // -----------------------------------------------------------------------------------------------------------------

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS / detach()
    // -----------------------------------------------------------------------------------------------------------------
    
    /**
     * detach() method must return resource provided in constructor.
     */
    public function test_detach()
    {
        $handle = fopen(__DIR__ . DIRECTORY_SEPARATOR . 'test.txt', 'w+');
        fwrite($handle, 'Hello world !');
        $stream = new Stream($handle);
        $this->assertSame(
            $handle,
            $stream->detach(),
            'detach() method must return resource provided in constructor.'
        );
    }
}
