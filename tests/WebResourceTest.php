<?php

namespace webignition\Tests\WebResource;

use GuzzleHttp\Message\MessageFactory;
use GuzzleHttp\Message\ResponseInterface;
use Mockery\MockInterface;
use PHPUnit_Framework_TestCase;
use webignition\InternetMediaType\InternetMediaType;
use webignition\WebResource\Exception;
use webignition\WebResource\WebResource;

class WebResourceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var WebResource
     */
    private $resource;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->resource = new WebResource();
    }

    /**
     * @dataProvider validContentTypeDataProvider
     *
     * @param InternetMediaType $contentType
     */
    public function testAddingContentTypeToResourceWithNoResponseIsAlwaysSuccessful(InternetMediaType $contentType)
    {
        $this->assertFalse($this->resource->hasHttpResponse());
        $this->assertCount(0, $this->resource->getValidContentTypes());

        $this->resource->addValidContentType($contentType);

        $validContentType = $this->resource->getValidContentTypes()[$contentType->getTypeSubtypeString()];

        $this->assertCount(1, $this->resource->getValidContentTypes());
        $this->assertEquals($contentType->getTypeSubtypeString(), $validContentType->getTypeSubtypeString());
    }

    /**
     * @dataProvider validContentTypeDataProvider
     *
     * @param InternetMediaType $contentType
     */
    public function testAddContentTypeThatInvalidatesHttpResponseContentTypeThrowsException(
        InternetMediaType $contentType
    ) {
        $this->resource->setHttpResponse(
            $this->createHttpResponseFromMessage("HTTP/1.0 200 OK\nContent-type:text/css")
        );

        $this->setExpectedException(Exception::class, 'HTTP response contains invalid content type', 2);
        $this->resource->addValidContentType($contentType);
    }

    public function testRemoveValidContentType()
    {
        $contentType = $this->createInternetMediaType('text', 'plain');
        $this->resource->addValidContentType($contentType);

        $this->assertCount(1, $this->resource->getValidContentTypes());

        $this->resource->removeValidContentType($contentType);

        $this->assertCount(0, $this->resource->getValidContentTypes());
    }

    /**
     * @dataProvider validContentTypeDataProvider
     *
     * @param InternetMediaType $contentType
     */
    public function testRemoveContentTypeThatInvalidatesHttpResponseContentTypeThrowsException(
        InternetMediaType $contentType
    ) {
        $this->resource->addValidContentType($this->createInternetMediaType('foo', 'bar'));
        $this->resource->addValidContentType($contentType);

        $this->resource->setHttpResponse(
            $this->createHttpResponseFromMessage(
                "HTTP/1.0 200 OK\nContent-type:" . $contentType->getTypeSubtypeString()
            )
        );

        $this->setExpectedException(Exception::class, 'HTTP response contains invalid content type', 2);
        $this->resource->removeValidContentType($contentType);
    }

    /**
     * @return array
     */
    public function validContentTypeDataProvider()
    {
        return [
            'text/plain' => [
                'contentType' => $this->createInternetMediaType('text', 'plain'),
            ],
            'text/html' => [
                'contentType' => $this->createInternetMediaType('text', 'html'),
            ],
            'application/octetstream' => [
                'contentType' => $this->createInternetMediaType('application', 'octetstream'),
            ],
        ];
    }

    public function testGetContentForResourceWithNoResponse()
    {
        $this->assertNull($this->resource->getContent());
    }

    /**
     * @dataProvider getContentDataProvider
     *
     * @param ResponseInterface $response
     * @param string $expectedContent
     */
    public function testGetContent(ResponseInterface $response, $expectedContent)
    {
        $this->resource->setHttpResponse($response);

        $this->assertEquals($expectedContent, $this->resource->getContent());
    }

    /**
     * @return array
     */
    public function getContentDataProvider()
    {
        return [
            'empty body' => [
                'response' => $this->createSuccessfulHttpResponseFromMessageBody(''),
                'expectedContent' => '',
            ],
            'non-empty body' => [
                'response' => $this->createSuccessfulHttpResponseFromMessageBody('foo'),
                'expectedContent' => 'foo',
            ],
            'gzipped body' => [
                'response' => $this->createHttpResponseFromMessage($this->loadFixture('gzipped-response.txt')),
                'expectedContent' => $this->loadFixture('decoded-gzipped-response-body.txt'),
            ],
        ];
    }

    /**
     * @dataProvider getContentTypeDataProvider
     *
     * @param ResponseInterface $response
     * @param string $expectedContentType
     */
    public function testGetContentType(ResponseInterface $response, $expectedContentType)
    {
        $this->resource->setHttpResponse($response);

        $this->assertEquals($expectedContentType, $this->resource->getContentType());
    }

    /**
     * @return array
     */
    public function getContentTypeDataProvider()
    {
        return [
            'no content type' => [
                'response' => $this->createHttpResponseFromMessage("HTTP/1.0 200 OK"),
                'expectedContentType' => '',
            ],
            'text/plain' => [
                'response' => $this->createHttpResponseFromMessage("HTTP/1.0 200 OK\nContent-type:text/plain"),
                'expectedContentType' => 'text/plain',
            ],
            'image/png' => [
                'response' => $this->createHttpResponseFromMessage("HTTP/1.0 200 OK\nContent-type:image/png"),
                'expectedContentType' => 'image/png',
            ],
        ];
    }

    public function testGetHttpResponseWithNoResponseSetThrowsException()
    {
        $this->setExpectedException(Exception::class, 'HTTP response not set', 1);
        $this->resource->getHttpResponse();
    }

    /**
     * @dataProvider getHttpResponseDataProvider
     *
     * @param ResponseInterface $httpResponse
     * @param $expectedContentType
     * @param $expectedContent
     */
    public function testGetHttpResponse(ResponseInterface $httpResponse, $expectedContentType, $expectedContent)
    {
        $this->resource->setHttpResponse($httpResponse);

        $this->assertEquals($httpResponse, $this->resource->getHttpResponse());
        $this->assertEquals($expectedContentType, $this->resource->getContentType());
        $this->assertEquals($expectedContent, $this->resource->getContent());
    }

    /**
     * @return array
     */
    public function getHttpResponseDataProvider()
    {
        return [
            'response 1' => [
                'httpResponse' => $this->createHttpResponseFromMessage(
                    "HTTP/1.0 200 OK\nContent-type:text/plain\n\nfoo"
                ),
                'expectedContentType' => 'text/plain',
                'expectedContent' => 'foo',
            ],
            'response 2' => [
                'httpResponse' => $this->createHttpResponseFromMessage(
                    "HTTP/1.0 200 OK\nContent-type:text/html\n\n<bar>"
                ),
                'expectedContentType' => 'text/html',
                'expectedContent' => '<bar>',
            ]
        ];
    }

    public function testGetUrlWithoutUrlSetWithoutHttpResponseSet()
    {
        $this->assertNull($this->resource->getUrl());
    }

    /**
     * @dataProvider getUrlWithoutHttpResponseDataProvider
     *
     * @param string $url
     * @param string $expectedUrl
     */
    public function testGetUrlWithUrlSetWithoutHttpResponseSet($url, $expectedUrl)
    {
        $this->resource->setUrl($url);

        $this->assertEquals($expectedUrl, $this->resource->getUrl());
    }

    /**
     * @return array
     */
    public function getUrlWithoutHttpResponseDataProvider()
    {
        return [
            [
                'url' => 'http://example.com/foo',
                'expectedUrl' => 'http://example.com/foo',
            ],
            [
                'url' => 'http://example.com/bar',
                'expectedUrl' => 'http://example.com/bar',
            ],
        ];
    }

    /**
     * @dataProvider getUrlWithHttpResponseDataProvider
     *
     * @param $responseUrl
     * @param $expectedUrl
     */
    public function testGetUrlWithHttpResponseSet($responseUrl, $expectedUrl)
    {
        /* @var $response ResponseInterface|MockInterface */
        $response = \Mockery::mock(ResponseInterface::class);
        $response
            ->shouldReceive('getEffectiveUrl')
            ->andReturn($responseUrl);

        $this->resource->setHttpResponse($response);

        $this->assertEquals($expectedUrl, $this->resource->getUrl());
    }

    /**
     * @return array
     */
    public function getUrlWithHttpResponseDataProvider()
    {
        return [
            [
                'responseUrl' => 'http://example.com/foo/bar',
                'expectedUrl' => 'http://example.com/foo/bar',
            ],
            [
                'responseUrl' => 'http://example.info/foo/bar',
                'expectedUrl' => 'http://example.info/foo/bar',
            ],
        ];
    }

    public function testHasValidContentTypeForEmptyContentTypes()
    {
        $this->assertTrue($this->resource->hasValidContentType());
    }

    /**
     * @dataProvider hasValidContentTypeDataProvider
     *
     * @param ResponseInterface $response
     * @param InternetMediaType[] $validContentTypes
     */
    public function testHasValidContentType(ResponseInterface $response, $validContentTypes)
    {
        foreach ($validContentTypes as $validContentType) {
            $this->resource->addValidContentType($validContentType);
        }

        $this->resource->setHttpResponse($response);

        $this->assertTrue($this->resource->hasValidContentType());
    }

    /**
     * @return array
     */
    public function hasValidContentTypeDataProvider()
    {
        return [
            'single' => [
                'response' => $this->createHttpResponseFromMessage("HTTP/1.1 200 OK\nContent-type:text/plain"),
                'validContentTypes' => [
                    $this->createInternetMediaType('text', 'plain'),
                ],
            ],
            'multiple' => [
                'response' => $this->createHttpResponseFromMessage("HTTP/1.1 200 OK\nContent-type:text/plain"),
                'validContentTypes' => [
                    $this->createInternetMediaType('text', 'html'),
                    $this->createInternetMediaType('image', 'png'),
                    $this->createInternetMediaType('text', 'plain'),
                ],
            ],
        ];
    }

    /**
     * @dataProvider invalidContentTypeDataProvider
     *
     * @param ResponseInterface $response
     * @param $validContentTypes
     */
    public function testHasInvalidContentType(ResponseInterface $response, $validContentTypes)
    {
        foreach ($validContentTypes as $validContentType) {
            $this->resource->addValidContentType($validContentType);
        }

        $this->setExpectedException(Exception::class, 'HTTP response contains invalid content type', 2);
        $this->resource->setHttpResponse($response);
    }

    /**
     * @return array
     */
    public function invalidContentTypeDataProvider()
    {
        return [
            'single' => [
                'response' => $this->createHttpResponseFromMessage("HTTP/1.1 200 OK\nContent-type:text/html"),
                'validContentTypes' => [
                    $this->createInternetMediaType('text', 'plain'),
                ],
            ],
            'multiple' => [
                'response' => $this->createHttpResponseFromMessage("HTTP/1.1 200 OK\nContent-type:text/plain"),
                'validContentTypes' => [
                    $this->createInternetMediaType('text', 'html'),
                    $this->createInternetMediaType('image', 'png'),
                    $this->createInternetMediaType('text', 'css'),
                ],
            ],
        ];
    }

    public function testSetHttpResponseWithInvalidContentTypeThrowsException()
    {
        $this->resource->addValidContentType($this->createInternetMediaType('text', 'html'));

        $this->setExpectedException(Exception::class, 'HTTP response contains invalid content type', 2);
        $this->resource->setHttpResponse(
            $this->createHttpResponseFromMessage("HTTP/1.1 200 OK\nContent-type:image/png")
        );
    }

    /**
     * @param string $type
     * @param string $subtype
     * @return InternetMediaType
     */
    private function createInternetMediaType($type, $subtype)
    {
        $internetMediaType = new InternetMediaType();
        $internetMediaType->setType($type);
        $internetMediaType->setSubtype($subtype);

        return $internetMediaType;
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    private function loadFixture($filename)
    {
        return file_get_contents(__DIR__ . '/fixtures/' . $filename);
    }

    /**
     * @param $message
     *
     * @return ResponseInterface
     */
    protected function createHttpResponseFromMessage($message)
    {
        $factory = new MessageFactory();

        return $factory->fromMessage($message);
    }

    /**
     * @param string $body
     *
     * @return ResponseInterface
     */
    protected function createSuccessfulHttpResponseFromMessageBody($body)
    {
        $factory = new MessageFactory();

        return $factory->fromMessage("HTTP/1.0 200 OK\n\n" . $body);
    }
}
