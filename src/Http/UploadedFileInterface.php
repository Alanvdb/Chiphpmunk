<?php

namespace Chiphpmunk\Http;

use Chiphpmunk\Stream\StreamInterface;

use RuntimeException;

interface UploadedFileInterface
{
    /**
     * Constructor
     * 
     * @param string   $name    Uploaded file name
     * @param string   $type    Uploaded file MIME type
     * @param string   $tmpName Uploaded file temporary URI
     * @param int      $error   One of PHP's UPLOAD_ERR_XXX constants
     * @param int|null $size    Uploaded file size
     * 
     * @throws InvalidArgumentException If any argument is invalid.
     */
    public function __construct(string $name, string $type, string $tmpName, int $error, ?int $size);

    /**
     * @return int One of PHP's UPLOAD_ERR_XXX constants.
     * If the file was uploaded successfully, this method returns UPLOAD_ERR_OK.
     * 
     * @see http://php.net/manual/en/features.file-upload.errors.php
     */
    public function getError() : int;

    /**
     * Moves the uploaded file to a new location.
     * 
     * @param string $targetPath Path chosen for the uploaded file
     * 
     * @throws InvalidArgumentException If the $targetPath specified is invalid.
     * Provided file name must match "`^[-0-9A-Z_\.]+$`i" pattern and max size is 255 bytes.
     * Specified target directory must exist.
     * @throws RuntimeException         On any error during the move operation.
     * 
     * @return void
     */
    public function moveTo(string $targetPath) : void;

    /**
     * Retrieve the file size.
     *
     * Implementations SHOULD return the value stored in the "size" key of
     * the file in the $_FILES array if available, as PHP calculates this based
     * on the actual size transmitted.
     *
     * @return int|null The file size in bytes or null if unknown.
     */
    public function getSize() : ?int;

    /**
     * Retrieve the filename sent by the client.
     *
     * Do not trust the value returned by this method. A client could send
     * a malicious filename with the intention to corrupt or hack your
     * application.
     *
     * Implementations SHOULD return the value stored in the "name" key of
     * the file in the $_FILES array.
     *
     * @return string|null The filename sent by the client or null if none was provided.
     */
    public function getClientFilename() : ?string;

    /**
     * Retrieve the media type sent by the client.
     *
     * Do not trust the value returned by this method. A client could send
     * a malicious media type with the intention to corrupt or hack your
     * application.
     *
     * @return string|null The media type sent by the client or null if none was provided.
     */
    public function getClientMediaType() : ?string;

    /**
     * @throws RuntimeException in cases when no stream is available or can be created.
     * If the moveTo() method has been called previously, this method will raise an exception.
     *
     * @return StreamInterface Stream representation of the uploaded file.
     */
    public function getStream() : StreamInterface;
}
