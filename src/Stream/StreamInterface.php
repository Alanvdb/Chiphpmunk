<?php

namespace Chiphpmunk\Stream;

interface StreamInterface
{
    /**
     * Constructor
     * 
     * @param resource|string $resource A resource or a string to open in a temporary stream in r+ mode
     * 
     * @throws RuntimeException         If cannot open a temporary stream
     * @throws InvalidArgumentException If argument is not a string or a resource
     */
    public function __construct($resource = null);

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > STREAM METADATA
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return int|null Stream size if it's possible
     */
    public function getSize() : ?int;

    /**
     * @return mixed Stream metadata
     */
    public function getMetadata() : array;

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > SEEK
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return bool Whether the stream is seekable
     */
    public function isSeekable() : bool;

    /**
     * Places stream pointer at the specified position
     * 
     * @see http://www.php.net/manual/en/function.fseek.php
     * 
     * @param int $offset Position to place stream pointer
     * @param int $whence 
     * SEEK_SET - Set position equal to offset bytes.
     * SEEK_CUR - Set position to current location plus offset.
     * SEEK_END - Set position to end-of-file plus offset.
     * 
     * @throws RuntimeException If cannot seek the specified position
     * 
     * @return void
     */
    public function seek(int $offset, int $whence = SEEK_SET) : void;

    /**
     * Places stream pointer at the begining
     * 
     * @throws RuntimeException If cannot rewind
     * 
     * @return void
     */
    public function rewind() : void;

    /**
     * @throws RuntimeException If cannot find the current pointer position
     * 
     * @return int Current position of the pointer
     */
    public function tell() : int;

    /**
     * @return bool Whether the stream is at the end.
     */
    public function eof() : bool;

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > READ
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return bool Whether the stream is readable
     */
    public function isReadable() : bool;

    /**
     * Reads the stream
     * 
     * @param int $maxBytes Maximum bytes to read
     * 
     * @throws RuntimeException If cannot read the stream
     * 
     * @return string
     */
    public function read(int $maxBytes) : string;

    /**
     * Reads remainder of a stream into a string
     * 
     * @throws RuntimeException on error while reading
     * 
     * @return string the remaining content
     */
    public function getContents() : string;

    /**
     * @return string Stream contents or an empty string on failure
     */
    public function __toString() : string;

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > WRITE
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return bool Whether the stream is writable
     */
    public function isWritable() : bool;

    /**
     * Writes in the stream
     * 
     * @param string $data Data to write in the stream
     * 
     * @throws RuntimeException If cannot write in the stream
     * 
     * @return int Number of bytes written
     */
    public function write(string $data) : int;

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > CLOSE
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Closes the stream
     * 
     * @return void
     */
    public function close() : void;

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach();
}
