<?php

namespace Chiphpmunk\Stream;

use RuntimeException;
use InvalidArgumentException;

class Stream implements StreamInterface
{
    // =================================================================================================================
    //
    //      ATTRIBUTES
    //
    // =================================================================================================================

    /**
     * @var resource $handle Stream resource
     */
    private $handle;

    /**
     * @var bool|null $isReadable Whether the stream is readable
     */
    private $isReadable;

    /**
     * @var bool|null $isWritable Whether the stream is writable
     */
    private $isWritable;

    /**
     * @var mixed[] $metaData Stream metadata
     */
    private $metadata;

    /**
     * @var int|null $size Stream size
     */
    private $size;

    // =================================================================================================================
    //
    //      METHODS
    //
    // =================================================================================================================

    /**
     * Constructor
     * 
     * @param resource|string $resource A resource or a string to open in a temporary stream in r+ mode
     * 
     * @throws RuntimeException         If cannot open a temporary stream
     * @throws InvalidArgumentException If argument is not a string or a resource
     */
    public function __construct($resource = '')
    {
        if (is_string($resource) ) {
            if (($this->handle = fopen('php://temp', 'r+')) === false) {
                throw new RuntimeException('Cannot open a new temporary stream.');
            }
            if ($resource !== '') {
                $this->write($resource);
            }
        } elseif (!is_resource($resource)) {
            throw new InvalidArgumentException('Argument must be a string or a resource.');
        } else {
            $this->handle = $resource;
        }
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > STREAM METADATA
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return int|null Stream size if it's possible
     */
    public function getSize() : ?int
    {
        if ($this->size === null && is_resource($this->handle)) {
            $this->size = fstat($this->handle)['size'] ?? null;
        }
        return $this->size;
    }

    /**
     * @return mixed Stream metadata
     */
    public function getMetadata() : array
    {
        if ($this->metadata === null) {
            $this->metadata = stream_get_meta_data($this->handle);
        }
        return $this->metadata;
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > SEEK
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return bool Whether the stream is seekable
     */
    public function isSeekable() : bool
    {
        return $this->getMetadata()['seekable'];
    }

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
    public function seek(int $offset, int $whence = SEEK_SET) : void
    {
        if (fseek($this->handle, $offset, $whence) === -1) {
            throw new RuntimeException('Cannot seek the specified position.');
        }
    }

    /**
     * Places stream pointer at the begining
     * 
     * @throws RuntimeException If cannot rewind
     * 
     * @return void
     */
    public function rewind() : void
    {
        $this->seek(0);
    }

    /**
     * @throws RuntimeException If cannot find the current pointer position
     * 
     * @return int Current position of the pointer
     */
    public function tell() : int
    {
        if (($position = ftell($this->handle)) === false) {
            throw new RuntimeException('Cannot find the current pointer position');
        }
        return $position;
    }

    /**
     * @return bool Whether the stream is at the end.
     */
    public function eof() : bool
    {
        return feof($this->handle);
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > READ
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return bool Whether the stream is readable
     */
    public function isReadable() : bool
    {
        if ($this->isReadable === null) {
            $this->isReadable = in_array(
                $this->getMetadata()['mode'],
                ['r', 'r+', 'w+', 'a+', 'x+', 'c+']
            );
        }
        return $this->isReadable;
    }

    /**
     * Reads the stream
     * 
     * @param int $maxBytes Maximum bytes to read
     * 
     * @throws RuntimeException If cannot read the stream
     * 
     * @return string
     */
    public function read(int $maxBytes) : string
    {
        if ($maxBytes < 0) {
            $maxBytes = 0;
        }
        if (($data = fread($this->handle, $maxBytes)) === false) {
            throw new RuntimeException('Cannot read the stream.');
        }
        return $data;
    }

    /**
     * Reads remainder of a stream into a string
     * 
     * @throws RuntimeException on error while reading
     * 
     * @return string the remaining content
     */
    public function getContents() : string
    {
        if (($contents = stream_get_contents($this->handle)) === false) {
            throw new RuntimeException('Cannot read the stream');
        }
        return $contents;
    }

    /**
     * @return string Stream contents or an empty string on failure
     */
    public function __toString() : string
    {
        try {
            $this->rewind();
            return $this->getContents();
        } catch (RuntimeException $e) {
            return '';
        }
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > WRITE
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return bool Whether the stream is writable
     */
    public function isWritable() : bool
    {
        if ($this->isWritable === null) {
            $this->isWritable = in_array(
                $this->getMetadata()['mode'],
                ['r+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+']
            );
        }
        return $this->isWritable;
    }

    /**
     * Writes in the stream
     * 
     * @param string $data Data to write in the stream
     * 
     * @throws RuntimeException If cannot write in the stream
     * 
     * @return int Number of bytes written
     */
    public function write(string $data) : int
    {
        if (($bytes = fwrite($this->handle, $data)) === false) {
            throw new RuntimeException('Cannot write in the stream.');
        }
        return $bytes;
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > CLOSE
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Closes the stream
     * 
     * @return void
     */
    public function close() : void
    {
        fclose($this->handle);
        $this->isReadable = false;
        $this->isWritable = false;
        $this->metadata['seekable'] = false;
    }

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach()
    {
        if (is_resource($this->handle)) {
            $returnedResource = $this->handle;
            $this->close();
            return $returnedResource;
        }
        return null;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        if (is_resource($this->handle)) {
            $this->close();
        }
    }
}
