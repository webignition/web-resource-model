<?php

namespace webignition\Tests\WebResource;

use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use webignition\InternetMediaType\InternetMediaType;
use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;
use webignition\WebResource\Exception\ReadOnlyResponseException;
use webignition\WebResource\Exception\UnseekableResponseException;
use webignition\WebResource\WebResource;

class WebResourceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createFromContentDataProvider
     *
     * @param InternetMediaTypeInterface|null $contentType
     */
    public function testCreateFromContent(?InternetMediaTypeInterface $contentType)
    {
        /* @var UriInterface|MockInterface $uri */
        $uri = \Mockery::mock(UriInterface::class);

        $content = 'resource content';

        $webResource = WebResource::createFromContent($uri, $content, $contentType);

        $this->assertEquals($uri, $webResource->getUri());
        $this->assertEquals($contentType, $webResource->getContentType());
        $this->assertEquals($content, $webResource->getContent());
        $this->assertNull($webResource->getResponse());
    }

    public function createFromContentDataProvider(): array
    {
        $contentType = \Mockery::mock(InternetMediaTypeInterface::class);

        return [
            'has content type' => [
                'contentType' => $contentType,
            ],
            'no content type' => [
                'contentType' => null,
            ],
        ];
    }

    /**
     * @dataProvider createFromResponseDataProvider
     *
     * @param string $responseContentType
     * @param string $expectedContentType
     * @param bool $expectedHasInvalidContentType
     */
    public function testCreateFromResponse(
        string $responseContentType,
        string $expectedContentType,
        bool $expectedHasInvalidContentType
    ) {
        /* @var UriInterface|MockInterface $uri */
        $uri = \Mockery::mock(UriInterface::class);

        $responseBodyContent = 'response body content';

        $responseBody = \Mockery::mock(StreamInterface::class);
        $responseBody
            ->shouldReceive('__toString')
            ->andReturn($responseBodyContent);

        /* @var ResponseInterface|MockInterface $response */
        $response = \Mockery::mock(ResponseInterface::class);
        $response
            ->shouldReceive('getHeaderLine')
            ->with(WebResource::HEADER_CONTENT_TYPE)
            ->andReturn($responseContentType);

        $response
            ->shouldReceive('getBody')
            ->andReturn($responseBody);

        $webResource = WebResource::createFromResponse($uri, $response);

        $this->assertEquals($uri, $webResource->getUri());
        $this->assertEquals($expectedContentType, (string)$webResource->getContentType());
        $this->assertEquals($expectedHasInvalidContentType, $webResource->hasInvalidContentType());
        $this->assertEquals($responseBodyContent, $webResource->getContent());
        $this->assertEquals($response, $webResource->getResponse());
    }

    public function createFromResponseDataProvider()
    {
        return [
            'valid content type' => [
                'responseContentTypeString' => 'text/html',
                'expectedContentType' => 'text/html',
                'expectedHasInvalidContentType' => false,
            ],
            'unparseable content type' => [
                'responseContentTypeString' => 'f o o',
                'expectedContentType' => '',
                'expectedHasInvalidContentType' => true,
            ],
        ];
    }

    public function testSetUri()
    {
        /* @var UriInterface|MockInterface $currentUri */
        $currentUri = \Mockery::mock(UriInterface::class);

        /* @var InternetMediaTypeInterface|MockInterface $contentType */
        $contentType = \Mockery::mock(InternetMediaTypeInterface::class);

        $content = 'resource content';

        $webResource = WebResource::createFromContent($currentUri, $content, $contentType);

        /* @var UriInterface|MockInterface $newUri */
        $newUri = \Mockery::mock(UriInterface::class);

        $updatedWebResource = $webResource->setUri($newUri);

        $this->assertNotEquals(spl_object_hash($webResource), spl_object_hash($updatedWebResource));
        $this->assertEquals($currentUri, $webResource->getUri());
        $this->assertEquals($newUri, $updatedWebResource->getUri());
    }

    public function testSetContentTypeForResourceWithoutResponse()
    {
        /* @var UriInterface|MockInterface $uri */
        $uri = \Mockery::mock(UriInterface::class);

        /* @var InternetMediaTypeInterface|MockInterface $currentContentType */
        $currentContentType = \Mockery::mock(InternetMediaTypeInterface::class);

        $content = 'resource content';

        $webResource = WebResource::createFromContent($uri, $content, $currentContentType);

        /* @var InternetMediaTypeInterface|MockInterface $newContentType */
        $newContentType = \Mockery::mock(InternetMediaTypeInterface::class);

        $updatedWebResource = $webResource->setContentType($newContentType);

        $this->assertNotEquals(spl_object_hash($webResource), spl_object_hash($updatedWebResource));
        $this->assertEquals($currentContentType, $webResource->getContentType());
        $this->assertEquals($newContentType, $updatedWebResource->getContentType());
    }

    public function testSetContentTypeForResourceWithResponse()
    {
        /* @var UriInterface|MockInterface $uri */
        $uri = \Mockery::mock(UriInterface::class);

        $responseContentType = 'text/html';
        $responseBodyContent = 'response body content';

        $responseBody = \Mockery::mock(StreamInterface::class);
        $responseBody
            ->shouldReceive('__toString')
            ->andReturn($responseBodyContent);

        $updatedContentTypeString = 'foo/bar';
        $updatedContentType = new InternetMediaType();
        $updatedContentType->setType('foo');
        $updatedContentType->setSubtype('bar');

        /* @var ResponseInterface|MockInterface $updatedResponse */
        $updatedResponse = \Mockery::mock(ResponseInterface::class);
        $updatedResponse
            ->shouldReceive('getHeaderLine')
            ->with(WebResource::HEADER_CONTENT_TYPE)
            ->andReturn($updatedContentTypeString);

        /* @var ResponseInterface|MockInterface $response */
        $response = \Mockery::mock(ResponseInterface::class);
        $response
            ->shouldReceive('getHeaderLine')
            ->with(WebResource::HEADER_CONTENT_TYPE)
            ->andReturn($responseContentType);

        $response
            ->shouldReceive('getBody')
            ->andReturn($responseBody);

        $response
            ->shouldReceive('withHeader')
            ->with(WebResource::HEADER_CONTENT_TYPE, $updatedContentTypeString)
            ->andReturn($updatedResponse);

        $webResource = WebResource::createFromResponse($uri, $response);

        $updatedWebResource = $webResource->setContentType($updatedContentType);

        $this->assertNotEquals(spl_object_hash($webResource), spl_object_hash($updatedWebResource));
        $this->assertEquals(spl_object_hash($updatedResponse), spl_object_hash($updatedWebResource->getResponse()));
    }

    public function testSetContentForResourceWithoutResponse()
    {
        /* @var UriInterface|MockInterface $uri */
        $uri = \Mockery::mock(UriInterface::class);

        /* @var InternetMediaTypeInterface|MockInterface $contentType */
        $contentType = \Mockery::mock(InternetMediaTypeInterface::class);

        $currentContent = 'resource content';

        $webResource = WebResource::createFromContent($uri, $currentContent, $contentType);

        $newContent = 'updated resource content';

        $updatedWebResource = $webResource->setContent($newContent);

        $this->assertNotEquals(spl_object_hash($webResource), spl_object_hash($updatedWebResource));
        $this->assertEquals($currentContent, $webResource->getContent());
        $this->assertEquals($newContent, $updatedWebResource->getContent());
    }

    public function testSetContentForResourceWithReadOnlyResource()
    {
        /* @var UriInterface|MockInterface $uri */
        $uri = \Mockery::mock(UriInterface::class);

        $contentTypeString = 'text/html';

        $responseBodyContent = 'response body content';

        $responseBody = \Mockery::mock(StreamInterface::class);
        $responseBody
            ->shouldReceive('__toString')
            ->andReturn($responseBodyContent);

        $responseBody
            ->shouldReceive('isWritable')
            ->andReturn(false);

        /* @var ResponseInterface|MockInterface $response */
        $response = \Mockery::mock(ResponseInterface::class);
        $response
            ->shouldReceive('getHeaderLine')
            ->with(WebResource::HEADER_CONTENT_TYPE)
            ->andReturn($contentTypeString);

        $response
            ->shouldReceive('getBody')
            ->andReturn($responseBody);

        $responseWebResource = WebResource::createFromResponse($uri, $response);

        $this->expectException(ReadOnlyResponseException::class);

        $responseWebResource->setContent('');
    }

    public function testSetContentForUnseekableResource()
    {
        /* @var UriInterface|MockInterface $uri */
        $uri = \Mockery::mock(UriInterface::class);

        $contentTypeString = 'text/html';

        $responseBodyContent = 'response body content';

        $responseBody = \Mockery::mock(StreamInterface::class);
        $responseBody
            ->shouldReceive('__toString')
            ->andReturn($responseBodyContent);

        $responseBody
            ->shouldReceive('isWritable')
            ->andReturn(true);

        $responseBody
            ->shouldReceive('isSeekable')
            ->andReturn(false);

        /* @var ResponseInterface|MockInterface $response */
        $response = \Mockery::mock(ResponseInterface::class);
        $response
            ->shouldReceive('getHeaderLine')
            ->with(WebResource::HEADER_CONTENT_TYPE)
            ->andReturn($contentTypeString);

        $response
            ->shouldReceive('getBody')
            ->andReturn($responseBody);

        $responseWebResource = WebResource::createFromResponse($uri, $response);

        $this->expectException(UnseekableResponseException::class);

        $responseWebResource->setContent('');
    }

    public function testSetContentForWritableResource()
    {
        /* @var UriInterface|MockInterface $uri */
        $uri = \Mockery::mock(UriInterface::class);

        $contentTypeString = 'text/html';

        $responseBodyContent = 'response body content';
        $newResponseBodyContent = 'new response body content';

        /* @var StreamInterface|MockInterface $responseBody */
        $responseBody = \Mockery::mock(StreamInterface::class);
        $responseBody
            ->shouldReceive('__toString')
            ->andReturn($responseBodyContent);

        $responseBody
            ->shouldReceive('isWritable')
            ->andReturn(true);

        $responseBody
            ->shouldReceive('isSeekable')
            ->andReturn(true);

        $responseBody
            ->shouldReceive('rewind');

        $responseBody
            ->shouldReceive('write')
            ->with($newResponseBodyContent);

        /* @var ResponseInterface|MockInterface $newResponse */
        $newResponse = \Mockery::mock(ResponseInterface::class);
        $newResponse
            ->shouldReceive('getHeaderLine')
            ->with(WebResource::HEADER_CONTENT_TYPE)
            ->andReturn($contentTypeString);

        /* @var ResponseInterface|MockInterface $response */
        $response = \Mockery::mock(ResponseInterface::class);
        $response
            ->shouldReceive('getHeaderLine')
            ->with(WebResource::HEADER_CONTENT_TYPE)
            ->andReturn($contentTypeString);

        $response
            ->shouldReceive('getBody')
            ->andReturn($responseBody);

        $response
            ->shouldReceive('withBody')
            ->withArgs(function (StreamInterface $updatedResponseBody) use ($responseBody) {
                $this->assertNotEquals(spl_object_hash($updatedResponseBody), spl_object_hash($responseBody));
                return true;
            })
            ->andReturn($newResponse);

        $responseWebResource = WebResource::createFromResponse($uri, $response);

        $updatedResponseWebResource = $responseWebResource->setContent($newResponseBodyContent);

        $this->assertNotEquals(spl_object_hash($updatedResponseWebResource), spl_object_hash($responseWebResource));
    }

    public function testSetResponseForResourceWithoutResponse()
    {
        /* @var UriInterface|MockInterface $uri */
        $uri = \Mockery::mock(UriInterface::class);

        /* @var InternetMediaTypeInterface|MockInterface $contentType */
        $contentType = \Mockery::mock(InternetMediaTypeInterface::class);

        $content = 'resource content';

        $webResource = WebResource::createFromContent($uri, $content, $contentType);

        $this->assertNull($webResource->getResponse());

        /* @var ResponseInterface|MockInterface $response */
        $response = \Mockery::mock(ResponseInterface::class);
        $response
            ->shouldReceive('getHeaderLine')
            ->with(WebResource::HEADER_CONTENT_TYPE)
            ->andReturn('text/html');

        $updatedWebResource = $webResource->setResponse($response);

        $this->assertNull($webResource->getResponse());
        $this->assertEquals($response, $updatedWebResource->getResponse());
    }

    /**
     * @dataProvider modelsDataProvider
     *
     * @param string $contentTypeType
     * @param string $contentTypeSubtype
     */
    public function testModels($contentTypeType, $contentTypeSubtype)
    {
        $contentType = new InternetMediaType();
        $contentType->setType($contentTypeType);
        $contentType->setSubtype($contentTypeSubtype);

        $this->assertTrue(WebResource::models($contentType));
    }

    /**
     * @return array
     */
    public function modelsDataProvider()
    {
        return [
            'text/plain' => [
                'contentTypeType' => 'text',
                'contentTypeSubtype' => 'plain',
            ],
            'text/html' => [
                'contentTypeType' => 'text',
                'contentTypeSubtype' => 'html',
            ],
            'application/xml' => [
                'contentTypeType' => 'application',
                'contentTypeSubtype' => 'xml',
            ],
            'application/json' => [
                'contentTypeType' => 'application',
                'contentTypeSubtype' => 'json',
            ],
            'application/octetstream' => [
                'contentTypeType' => 'application',
                'contentTypeSubtype' => 'octetstream',
            ],
            'image/png' => [
                'contentTypeType' => 'image',
                'contentTypeSubtype' => 'png',
            ],
            'image/jpeg' => [
                'contentTypeType' => 'image',
                'contentTypeSubtype' => 'jpeg',
            ],
        ];
    }

    public function testGetModelledContentTypeStrings()
    {
        $this->assertEquals([], WebResource::getModelledContentTypeStrings());
    }
}
