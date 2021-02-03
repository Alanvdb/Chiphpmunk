<?php

use PHPUnit\Framework\TestCase;

use Chiphpmunk\Http\ServerRequest;
use Chiphpmunk\Http\UploadedFile;

class ServerRequestTest extends TestCase
{
    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS > __construct()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * ServerRequest must be instanciable without argument.
     */
    public function test_construct_withoutArgument()
    {
        $r = new ServerRequest();
        $this->assertInstanceOf(
            ServerRequest::class,
            $r,
            'ServerRequest must be instanciable without argument.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS > fromGlobals()
    // -----------------------------------------------------------------------------------------------------------------

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS > getServerParams()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * getServerParams() must return array.
     * 
     * @depends test_construct_withoutArgument
     */
    public function test_getServerParams_returnArray()
    {
        $r = new ServerRequest();
        $this->assertIsArray(
            $r->getServerParams(),
            'getServerParams() must return array.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS > getQueryParams()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * getQueryParams() must return an empty array if no uploaded file is provided.
     * 
     * @depends test_construct_withoutArgument
     */
    public function test_getQueryParams_withoutQuery()
    {
        $r = new ServerRequest();
        $this->assertSame(
            [],
            $r->getQueryParams(),
            'getQueryParams() must return an empty array if no uploaded file is provided.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS > withQueryParams()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * withQueryParams() must throw InvalidArgumentException if the array is not an array of string values.
     */
    public function test_withQueryParams_withInvalidArray()
    {
        $arg = ['test' => new stdClass()];

        $this->expectException(InvalidArgumentException::class);
        (new ServerRequest())->withQueryParams($arg);
    }

    /**
     * withQueryParams() must throw InvalidArgumentException if the multidimensional array is not an array
     * where each leaf is a string value.
     */
    public function test_withQueryParams_withInvalidMultiDimensionalArray()
    {
        $arg = ['test' => [new stdClass()]];

        $this->expectException(InvalidArgumentException::class);
        (new ServerRequest())->withQueryParams($arg);
    }

    /**
     * getQueryParams() must return array provided in withQueryParams().
     */
    public function test_withQueryParams_withValidArray()
    {
        $expected = ['param' => 'value'];
        $request = (new ServerRequest())->withQueryParams($expected);
        $this->assertSame(
            $expected,
            $request->getQueryParams(),
            'getQueryParams() must return array provided in withQueryParams().'
        );
    }

    /**
     * getQueryParams() must return multidimensional array provided in withQueryParams().
     */
    public function test_withQueryParams_withValidMultidimensionalArray()
    {
        $expected = ['param' => [['value']]];
        $request = (new ServerRequest())->withQueryParams($expected);
        $this->assertSame(
            $expected,
            $request->getQueryParams(),
            'getQueryParams() must return multidimensional array provided in withQueryParams().'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS > getCookieParams()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * getCookieParams() must return an empty array if no uploaded file is provided.
     * 
     * @depends test_construct_withoutArgument
     */
    public function test_getCookieParams_withoutCookie()
    {
        $r = new ServerRequest();
        $this->assertSame(
            [],
            $r->getCookieParams(),
            'getCookieParams() must return an empty array if no uploaded file is provided.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS > withCookieParams()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * withCookieParams() must throw InvalidArgumentException if cookie name contains spaces.
     */
    public function test_withCookieParams_withSpacesInName()
    {
        $this->expectException(InvalidArgumentException::class);
        (new ServerRequest())->withCookieParams(['my test' => 'test']);
    }

    /**
     * withCookieParams() must throw InvalidArgumentException if cookie name contains dots.
     */
    public function test_withCookieParams_withDotsInName()
    {
        $this->expectException(InvalidArgumentException::class);
        (new ServerRequest())->withCookieParams(['my.test' => 'test']);
    }

    /**
     * withCookieParams() must throw InvalidArgumentException if cookie value is not of type string.
     */
    public function test_withCookieParams_withInvalidValue()
    {
        $this->expectException(InvalidArgumentException::class);
        (new ServerRequest())->withCookieParams(['mytest' => []]);
    }

    /**
     * getQueryParams() must return value provided in withQueryParams().
     */
    public function test_withQueryParams_withValidArgument()
    {
        $expected = ['test' => 'test'];
        $request = (new ServerRequest())->withCookieParams($expected);
        $this->assertSame(
            $expected,
            $request->getCookieParams(),
            'getQueryParams() must return value provided in withQueryParams().'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS > getUploadedFiles()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * getUploadedFiles() must return an empty array if no uploaded file is provided.
     * 
     * @depends test_construct_withoutArgument
     */
    public function test_getUploadedFiles_withoutFiles()
    {
        $r = new ServerRequest();
        $this->assertSame(
            [],
            $r->getUploadedFiles(),
            'getUploadedFiles() must return an empty array if no uploaded file is provided.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS > withUploadedFiles()
    // -----------------------------------------------------------------------------------------------------------------

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS > normalizeUploadedFileArray()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * normalizeUploadedFileArray() must work with an array structured from an input where name="avatar".
     */
    public function test_normalizeUploadedFileArray_withSimpleArray()
    {
        $files = [
            'avatar' => [
                'tmp_name' => 'phpUxcOty',
                'name' => 'my-avatar.png',
                'size' => 90996,
                'type' => 'image/png',
                'error' => 0
            ]
        ];
        $expected = ['avatar' => new UploadedFile('my-avatar.png', 'image/png', 'phpUxcOty', 0, 90996)];
        $this->assertEquals(
            $expected,
            ServerRequest::normalizeUploadedFileArray($files),
            'normalizeUploadedFileArray() must work with an array structured from an input where name="file".'
        );
    }

    /**
     * normalizeUploadedFileArray() must work with an array structured from an input where name="file[details][avatar]".
     */
    public function test_normalizeUploadedFileArray_withMultidimensionalArray()
    {
        $files = [
            'my-form' => [
                'name'     => ['details' => ['avatar' => 'my-avatar.png']],
                'type'     => ['details' => ['avatar' => 'image/png']],
                'tmp_name' => ['details' => ['avatar' => 'phpmFLrzD']],
                'error'    => ['details' => ['avatar' => 0]],
                'size'     => ['details' => ['avatar' => 90996]]
            ]
        ];
        $expected = [
            'my-form' => [
                'details' => [
                    'avatar' => new UploadedFile('my-avatar.png', 'image/png', 'phpmFLrzD', 0, 90996)
                ]
            ]
        ];
        $this->assertEquals(
            $expected,
            ServerRequest::normalizeUploadedFileArray($files),
            'normalizeUploadedFileArray() must work with an array structured from an input where name="file[details][avatar]".'
        );
    }

    /**
     * normalizeUploadedFileArray() must work with an array structured from an input where name="my-form[details][avatars][]".
     */
    public function test_normalizeUploadedFileArray_withMultidimensionalArray2()
    {
        $files = [
            'my-form' => [
                'name' => [
                    'details' => [
                        'avatar' => [0 => 'my-avatar.png', 1 => 'my-avatar2.png', 2 => 'my-avatar3.png']
                    ]
                ],
                'type' => [
                    'details' => [
                        'avatar' => [0 => 'image/png', 1 => 'image/png', 2 => 'image/png']
                    ]
                ],
                'tmp_name' => [
                    'details' => [
                        'avatar' => [0 => 'phpmFLrzD', 1 => 'phpV2pBil', 2 => 'php8RUG8v']
                    ]
                ],
                'error' => [
                    'details' => [
                        'avatar' => [0 => 0, 1 => 0, 2 => 0]
                    ]
                ],
                'size' => [
                    'details' => [
                        'avatar' => [0 => 90996, 1 => 90996, 2 => 90996]
                    ]
                ]
            ]
        ];

        // Expected array
        $expected = [
            'my-form' => ['details' => ['avatar' => [
                0 => new UploadedFile('my-avatar.png', 'image/png', 'phpmFLrzD', 0, 90996),
                1 => new UploadedFile('my-avatar2.png', 'image/png', 'phpV2pBil', 0, 90996),
                2 => new UploadedFile('my-avatar3.png', 'image/png', 'php8RUG8v', 0, 90996)
            ]]]
        ];

        $this->assertEquals(
            $expected,
            ServerRequest::normalizeUploadedFileArray($files),
            'normalizeUploadedFileArray() must work with an array structured from an input where name="my-form[details][avatars][]".'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS > getParsedBody()
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * getParsedBody() must return null if no body was specified.
     * 
     * @depends test_construct_withoutArgument
     */
    public function test_getParsedBody_withoutBody()
    {
        $r = new ServerRequest();
        $this->assertNull(
            $r->getParsedBody(),
            'getParsedBody() must return null if no body was specified.'
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS > withParsedBody()
    // -----------------------------------------------------------------------------------------------------------------

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS > getAttribute()
    // -----------------------------------------------------------------------------------------------------------------

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS > getAttributes()
    // -----------------------------------------------------------------------------------------------------------------

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS > withAttribute()
    // -----------------------------------------------------------------------------------------------------------------

    // -----------------------------------------------------------------------------------------------------------------
    //      TESTS > withoutAttribute()
    // -----------------------------------------------------------------------------------------------------------------
}
