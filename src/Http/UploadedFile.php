<?php declare(strict_types=1);

namespace Chiphpmunk\Http;

use Chiphpmunk\Stream\StreamInterface;
use Chiphpmunk\Stream\Stream;

use InvalidArgumentException;
use RuntimeException;
use Exception;

class UploadedFile implements UploadedFileInterface
{
    // =================================================================================================================
    //
    //      ERROR CODES
    //
    // =================================================================================================================

    /**
     * @const int HIGHER_THAN_INI_SIZE
     * Error code thrown when uploaded file size exceed size in php.ini
     */
    public const HIGHER_THAN_INI_SIZE = UPLOAD_ERR_INI_SIZE;

    /**
     * @const int HIGHER_THAN_FORM_SIZE
     * Error code thrown when uploaded file size exceed size specified in HTML form
     */
    public const HIGHER_THAN_FORM_SIZE = UPLOAD_ERR_FORM_SIZE;

    /**
     * @const int PARTIALLY_UPLOADED
     * Error code thrown when file was partially uploaded
     */
    public const PARTIALLY_UPLOADED = UPLOAD_ERR_PARTIAL;

    /**
     * @const int NO_FILE
     * Error code thrown when no file was uploaded
     */
    public const NO_FILE = UPLOAD_ERR_NO_FILE;

    /**
     * @const int NO_TEMPORARY_DIRECTORY
     * Error code thrown when temporary directory is missing
     */
    public const NO_TEMPORARY_DIRECTORY = UPLOAD_ERR_NO_TMP_DIR;

    /**
     * @const int WRITING_FAILED
     * Error code thrown when couldn't write to disk
     */
    public const WRITING_FAILED = UPLOAD_ERR_CANT_WRITE;

    /**
     * @const int STOPPED_BY_EXTENSION
     * Error code thrown when a PHP extension stopped the upload
     */
    public const STOPPED_BY_EXTENSION = UPLOAD_ERR_EXTENSION;

    /**
     * @const int UNEXPECTED_ERROR
     * Error code thrown when uploaded file send an unexpected error
     */
    public const UNEXPECTED_ERROR = 100;

    /**
     * @const int ALREADY_MOVED
     * Error code thrown when Uploaded file has already been moved.
     */
    public const ALREADY_MOVED = 101;

    // =================================================================================================================
    //
    //      ATTRIBUTES
    //
    // =================================================================================================================
    
    /**
     * @var string|null $name Uploaded file name
     */
    private $name;

    /**
     * @var string|null $type Uploaded file MIME type
     */
    private $type;

    /**
     * @var string $tmpName Uploaded file temporary URI
     */
    private $tmpName;

    /**
     * @var int $error One of PHP's UPLOAD_ERR_XXX constants
     * 
     * @see http://php.net/manual/en/features.file-upload.errors.php
     */
    private $error;

    /**
     * @var int|null $size Uploaded file size (null if unknown)
     */
    private $size;

    /**
     * @var bool $hasMoved Whether or not temporary file has been moved
     */
    private $hasMoved = false;

    // =================================================================================================================
    //
    //      METHODS
    //
    // =================================================================================================================

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
    public function __construct(string $name, string $type, string $tmpName, int $error, ?int $size)
    {
        if ($name === '') {
            throw new InvalidArgumentException('Uploaded file name cannot be empty.');
        }
        if ($error < 0 || $error > 8) {
            throw new InvalidArgumentException(
                "Provided error argument must be one of PHP's UPLOAD_ERR_XXX constants.\n"
                . "See http://php.net/manual/en/features.file-upload.errors.php for more infos."
            );
        }
        if ($size < 0) {
            throw new InvalidArgumentException('Invalid file size provided: "' . $size . '".');
        }
        $this->name    = $name;
        $this->type    = $type === '' ? null : $type;
        $this->tmpName = $tmpName;
        $this->error   = $error;
        $this->size    = $size;
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > FILE ASSERTION
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return int One of PHP's UPLOAD_ERR_XXX constants.
     * If the file was uploaded successfully, this method returns UPLOAD_ERR_OK.
     * 
     * @see http://php.net/manual/en/features.file-upload.errors.php
     */
    public function getError() : int
    {
        return $this->error;
    }

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
    public function moveTo(string $targetPath) : void
    {
        if ($this->hasMoved) {
            throw new RuntimeException('Uploaded file has already been moved.', self::ALREADY_MOVED);
        }
        if ($this->getError() !== UPLOAD_ERR_OK) {
            switch ($this->error) {
                case self::HIGHER_THAN_INI_SIZE:
                    throw new RuntimeException(
                        'The uploaded file exceeds the upload_max_filesize directive in "php.ini".',
                        self::HIGHER_THAN_INI_SIZE
                    );
                break;
                case self::HIGHER_THAN_FORM_SIZE:
                    throw new RuntimeException(
                        'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
                        self::HIGHER_THAN_FORM_SIZE
                    );
                break;
                case self::PARTIALLY_UPLOADED:
                    throw new RuntimeException(
                        'The uploaded file was only partially uploaded.',
                        self::PARTIALLY_UPLOADED
                    );
                break;
                case self::NO_FILE:
                    throw new RuntimeException('No file was uploaded.', self::NO_FILE);
                break;
                case self::NO_TEMPORARY_DIRECTORY:
                    throw new RuntimeException('Missing a temporary folder.', self::NO_TEMPORARY_DIRECTORY);
                break;
                case self::WRITING_FAILED:
                    throw new RuntimeException('Failed to write file to disk.', self::WRITING_FAILED);
                break;
                case self::STOPPED_BY_EXTENSION:
                    throw new RuntimeException('A PHP extension stopped the file upload.', self::STOPPED_BY_EXTENSION);
                break;
                default:
                    throw new RuntimeException('Unknown file upload error.', self::UNEXPECTED_ERROR);
            }
        }
        $targetName = basename($targetPath);
        if (!preg_match('`^[-0-9A-Z_\.]+$`i', $targetName)) {
            throw new InvalidArgumentException(
                'Provided file name must match "`^[-0-9A-Z_\.]+$`i" pattern. Provided file name: "' . $targetName . '".');
        }
        if (strlen($targetName) > 255) {
            throw new InvalidArgumentException('Provided file name is too long (255 bytes max): "' . $targetName . '".');
        }
        if (!is_dir(dirname($targetPath))) {
            throw new InvalidArgumentException('Specified target directory does not exist: "' . dirname($targetPath) . '".');
        }
        if (move_uploaded_file($this->tmpName, $targetPath) === false) {
            throw new RuntimeException('An error occured during move operation.', self::UNEXPECTED_ERROR);
        }
        $this->hasMoved = true;
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      METHODS > FILE INFOS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return int|null The file size in bytes or null if unknown.
     */
    public function getSize() : ?int
    {
        return $this->size;
    }

    /**
     * @return string|null The filename sent by the client or null if none was provided.
     * 
     * Do not trust the value returned by this method. A client could send
     * a malicious filename with the intention to corrupt or hack your
     * application.
     */
    public function getClientFilename() : ?string
    {
        return $this->name;
    }

    /**
     * @return string|null The media type sent by the client or null if none was provided.
     * Do not trust the value returned by this method. A client could send
     * a malicious media type with the intention to corrupt or hack your
     * application.
     */
    public function getClientMediaType() : ?string
    {
        return $this->type;
    }

    /**
     * @return string Temporary file path
     */
    public function getTemporaryFile() : string
    {
        return $this->tmpName;
    }

    /**
     * @throws RuntimeException in cases when no stream is available or can be created.
     * If the moveTo() method has been called previously, this method will raise an exception.
     *
     * @return StreamInterface Stream representation of the uploaded file.
     */
    public function getStream() : StreamInterface
    {
        try {
            return new Stream(fopen($this->getTemporaryFile(), 'r'));
        } catch (Exception $e) {
            throw new RuntimeException('Cannot create stream for uploaded file', 0, $e);
        }
    }
}
