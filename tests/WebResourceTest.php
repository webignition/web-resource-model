<?php

namespace webignition\Tests\WebResource;

use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use webignition\InternetMediaType\InternetMediaType;
use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;
use webignition\WebResource\Exception\InvalidContentTypeException;
use webignition\WebResource\Exception\ReadOnlyResponseException;
use webignition\WebResource\Exception\UnseekableResponseException;
use webignition\WebResource\WebResource;
use webignition\WebResource\WebResourceProperties;

class WebResourceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createFromContentDataProvider
     *
     * @param InternetMediaTypeInterface|null $contentType
     *
     * @throws InvalidContentTypeException
     */
    public function testCreateFromContent(?InternetMediaTypeInterface $contentType)
    {
        /* @var UriInterface|MockInterface $uri */
        $uri = \Mockery::mock(UriInterface::class);

        $content = 'resource content';

        $webResource = new WebResource(WebResourceProperties::create([
            WebResourceProperties::ARG_URI => $uri,
            WebResourceProperties::ARG_CONTENT => $content,
            WebResourceProperties::ARG_CONTENT_TYPE => $contentType,
        ]));

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
     *
     * @throws InvalidContentTypeException
     */
    public function testCreateFromResponse(
        string $responseContentType,
        string $expectedContentType,
        bool $expectedHasInvalidContentType
    ) {
        $uri = \Mockery::mock(UriInterface::class);

        $responseBodyContent = 'response body content';

        $responseBody = \Mockery::mock(StreamInterface::class);
        $responseBody
            ->shouldReceive('__toString')
            ->andReturn($responseBodyContent);

        $response = \Mockery::mock(ResponseInterface::class);
        $response
            ->shouldReceive('getHeaderLine')
            ->with(WebResource::HEADER_CONTENT_TYPE)
            ->andReturn($responseContentType);

        $response
            ->shouldReceive('getBody')
            ->andReturn($responseBody);

        $webResource = new WebResource(WebResourceProperties::create([
            WebResourceProperties::ARG_URI => $uri,
            WebResourceProperties::ARG_RESPONSE => $response,
        ]));

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

    /**
     * @throws InvalidContentTypeException
     * @throws ReadOnlyResponseException
     * @throws UnseekableResponseException
     */
    public function testSetContentForResourceWithReadOnlyResource()
    {
        $contentTypeString = 'text/html';

        $responseBodyContent = 'response body content';

        $responseBody = \Mockery::mock(StreamInterface::class);
        $responseBody
            ->shouldReceive('__toString')
            ->andReturn($responseBodyContent);

        $responseBody
            ->shouldReceive('isWritable')
            ->andReturn(false);

        $response = \Mockery::mock(ResponseInterface::class);
        $response
            ->shouldReceive('getHeaderLine')
            ->with(WebResource::HEADER_CONTENT_TYPE)
            ->andReturn($contentTypeString);

        $response
            ->shouldReceive('getBody')
            ->andReturn($responseBody);

        $webResource = new WebResource(WebResourceProperties::create([
            WebResourceProperties::ARG_RESPONSE => $response,
        ]));

        $this->expectException(ReadOnlyResponseException::class);

        $webResource->setContent('');
    }

    /**
     * @throws InvalidContentTypeException
     * @throws ReadOnlyResponseException
     * @throws UnseekableResponseException
     */
    public function testSetContentForUnseekableResource()
    {
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

        $response = \Mockery::mock(ResponseInterface::class);
        $response
            ->shouldReceive('getHeaderLine')
            ->with(WebResource::HEADER_CONTENT_TYPE)
            ->andReturn($contentTypeString);

        $response
            ->shouldReceive('getBody')
            ->andReturn($responseBody);

        $webResource = new WebResource(WebResourceProperties::create([
            WebResourceProperties::ARG_RESPONSE => $response,
        ]));

        $this->expectException(UnseekableResponseException::class);

        $webResource->setContent('');
    }

    /**
     * @dataProvider modelsDataProvider
     *
     * @param InternetMediaTypeInterface $contentType
     */
    public function testModels(InternetMediaTypeInterface $contentType)
    {
        $this->assertTrue(WebResource::models($contentType));
    }

    public function modelsDataProvider(): array
    {
        return [
            'text/plain' => [
                'internetMediaType' => new InternetMediaType('text', 'plain'),
            ],
            'text/html' => [
                'internetMediaType' => new InternetMediaType('text', 'html'),
            ],
            'application/xml' => [
                'internetMediaType' => new InternetMediaType('application', 'xml'),
            ],
            'application/json' => [
                'internetMediaType' => new InternetMediaType('application', 'json'),
            ],
            'application/octetstream' => [
                'internetMediaType' => new InternetMediaType('application', 'octetstream'),
            ],
            'image/png' => [
                'internetMediaType' => new InternetMediaType('image', 'png'),
            ],
            'image/jpeg' => [
                'internetMediaType' => new InternetMediaType('image', 'jpeg'),
            ],
        ];
    }

    public function testGetModelledContentTypeStrings()
    {
        $this->assertEquals([], WebResource::getModelledContentTypeStrings());
    }
}
