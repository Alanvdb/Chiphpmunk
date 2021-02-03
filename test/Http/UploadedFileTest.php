<?php

use PHPUnit\Framework\TestCase;

use Chiphpmunk\Http\UploadedFile;

class UploadedFileTest extends TestCase
{
    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS > __construct()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Constructor must throw InvalidArgumentException if provided name is empty
     */
    public function test_construct_withEmptyName()
    {
        $this->expectException(InvalidArgumentException::class);
        new UploadedFile('', 'text/html', 'file.html', UPLOAD_ERR_OK, 50);
    }

    /**
     * Constructor must throw InvalidArgumentException if provided size is negative.
     */
    public function test_construct_withNegativeSize()
    {
        $this->expectException(InvalidArgumentException::class);
        new UploadedFile('test.html', 'text/html', 'file.html', UPLOAD_ERR_OK, -50);
    }

    /**
     * Constructor must throw InvalidArgumentException if provided error is lower than UPLOAD_ERR_OK (0).
     */
    public function test_construct_withNegativeError()
    {
        $this->expectException(InvalidArgumentException::class);
        new UploadedFile('test.html', 'text/html', 'file.html', -1, 50);
    }

    /**
     * Constructor must throw InvalidArgumentException if provided error is higher than STOPPED_BY_EXTENSION (8).
     */
    public function test_construct_withHighError()
    {
        $this->expectException(InvalidArgumentException::class);
        new UploadedFile('test.html', 'text/html', 'file.html', 9, 50);
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS > getError()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * getError() must return UPLOAD_ERR_OK if UPLOAD_ERR_OK is provided in constructor.
     */
    public function test_getError_withoutError()
    {
        $file = new UploadedFile('test.html', 'text/html', 'file.html', UPLOAD_ERR_OK, 50);
        $this->assertSame(
            UPLOAD_ERR_OK,
            $file->getError(),
            'getError() must return UPLOAD_ERR_OK if UPLOAD_ERR_OK is provided in constructor.'
        );
    }

    /**
     * getError() must return UPLOAD_ERR_INI_SIZE if UPLOAD_ERR_INI_SIZE is provided in constructor.
     */
    public function test_getError_withError1()
    {
        $file = new UploadedFile('test.html', 'text/html', 'file.html', UPLOAD_ERR_INI_SIZE, 50);
        $this->assertSame(
            UPLOAD_ERR_INI_SIZE,
            $file->getError(),
            'getError() must return UPLOAD_ERR_INI_SIZE if UPLOAD_ERR_INI_SIZE is provided in constructor.'
        );
    }

    /**
     * getError() must return UPLOAD_ERR_FORM_SIZE if UPLOAD_ERR_FORM_SIZE is provided in constructor.
     */
    public function test_getError_withError2()
    {
        $file = new UploadedFile('test.html', 'text/html', 'file.html', UPLOAD_ERR_FORM_SIZE, 50);
        $this->assertSame(
            UPLOAD_ERR_FORM_SIZE,
            $file->getError(),
            'getError() must return UPLOAD_ERR_FORM_SIZE if UPLOAD_ERR_FORM_SIZE is provided in constructor.'
        );
    }

    /**
     * getError() must return UPLOAD_ERR_PARTIAL if UPLOAD_ERR_PARTIAL is provided in constructor.
     */
    public function test_getError_withError3()
    {
        $file = new UploadedFile('test.html', 'text/html', 'file.html', UPLOAD_ERR_PARTIAL, 50);
        $this->assertSame(
            UPLOAD_ERR_PARTIAL,
            $file->getError(),
            'getError() must return UPLOAD_ERR_PARTIAL if UPLOAD_ERR_PARTIAL is provided in constructor.'
        );
    }

    /**
     * getError() must return UPLOAD_ERR_NO_FILE if UPLOAD_ERR_NO_FILE is provided in constructor.
     */
    public function test_getError_withError4()
    {
        $file = new UploadedFile('test.html', 'text/html', 'file.html', UPLOAD_ERR_NO_FILE, 50);
        $this->assertSame(
            UPLOAD_ERR_NO_FILE,
            $file->getError(),
            'getError() must return UPLOAD_ERR_NO_FILE if UPLOAD_ERR_NO_FILE is provided in constructor.'
        );
    }

    /**
     * getError() must return UPLOAD_ERR_NO_TMP_DIR if UPLOAD_ERR_NO_TMP_DIR is provided in constructor.
     */
    public function test_getError_withError5()
    {
        $file = new UploadedFile('test.html', 'text/html', 'file.html', UPLOAD_ERR_NO_TMP_DIR, 50);
        $this->assertSame(
            UPLOAD_ERR_NO_TMP_DIR,
            $file->getError(),
            'getError() must return UPLOAD_ERR_NO_TMP_DIR if UPLOAD_ERR_NO_TMP_DIR is provided in constructor.'
        );
    }

    /**
     * getError() must return UPLOAD_ERR_CANT_WRITE if UPLOAD_ERR_CANT_WRITE is provided in constructor.
     */
    public function test_getError_withError6()
    {
        $file = new UploadedFile('test.html', 'text/html', 'file.html', UPLOAD_ERR_CANT_WRITE, 50);
        $this->assertSame(
            UPLOAD_ERR_CANT_WRITE,
            $file->getError(),
            'getError() must return UPLOAD_ERR_CANT_WRITE if UPLOAD_ERR_CANT_WRITE is provided in constructor.'
        );
    }

    /**
     * getError() must return UPLOAD_ERR_EXTENSION if UPLOAD_ERR_EXTENSION is provided in constructor.
     */
    public function test_getError_withError7()
    {
        $file = new UploadedFile('test.html', 'text/html', 'file.html', UPLOAD_ERR_EXTENSION, 50);
        $this->assertSame(
            UPLOAD_ERR_EXTENSION,
            $file->getError(),
            'getError() must return UPLOAD_ERR_EXTENSION if UPLOAD_ERR_EXTENSION is provided in constructor.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS > moveTo()
    // -----------------------------------------------------------------------------------------------------------------

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS > getSize()
    // -----------------------------------------------------------------------------------------------------------------

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS > getClientFilename()
    // -----------------------------------------------------------------------------------------------------------------

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS > getClientMediaType()
    // -----------------------------------------------------------------------------------------------------------------

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS > getTemporaryFile()
    // -----------------------------------------------------------------------------------------------------------------

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS > getStream()
    // -----------------------------------------------------------------------------------------------------------------
}
