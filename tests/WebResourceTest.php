<?php

namespace webignition\Tests\WebResource;

use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use webignition\InternetMediaType\InternetMediaType;
use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;
use webignition\WebResource\Exception\InvalidContentTypeException;
use webignition\WebResource\Exception\UnwritableContentException;
use webignition\WebResource\WebResource;
use webignition\WebResource\WebResourceProperties;

class WebResourceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @throws InvalidContentTypeException
     */
    public function testCreateWithNoArguments()
    {
        $webResource = new WebResource();

        $this->assertNull($webResource->getUri());
        $this->assertNull($webResource->getContentType());
        $this->assertNull($webResource->getContent());
        $this->assertNull($webResource->getResponse());
    }

    /**
     * @throws InvalidContentTypeException
     */
    public function testCreateFromContent()
    {
        $content = 'content';
        $contentType = new InternetMediaType('text', 'plain');

        $webResource = WebResource::createFromContent($content, $contentType);

        $this->assertInstanceOf(WebResource::class, $webResource);
        $this->assertEquals($content, $webResource->getContent());
        $this->assertEquals($contentType, $webResource->getContentType());
        $this->assertNull($webResource->getUri());
        $this->assertNull($webResource->getResponse());
    }

    /**
     * @throws InvalidContentTypeException
     */
    public function testCreateFromResponse()
    {
        $uri = \Mockery::mock(UriInterface::class);
        $contentTypeString = 'text/plain';
        $content = 'content';

        $responseBody = \Mockery::mock(StreamInterface::class);
        $responseBody
            ->shouldReceive('__toString')
            ->andReturn($content);

        $response = \Mockery::mock(ResponseInterface::class);
        $response
            ->shouldReceive('getHeaderLine')
            ->with(WebResource::HEADER_CONTENT_TYPE)
            ->andReturn($contentTypeString);

        $response
            ->shouldReceive('getBody')
            ->andReturn($responseBody);

        $webResource = WebResource::createFromResponse($uri, $response);

        $this->assertInstanceOf(WebResource::class, $webResource);
        $this->assertEquals($content, $webResource->getContent());
        $this->assertEquals($contentTypeString, $webResource->getContentType());
        $this->assertEquals($uri, $webResource->getUri());
        $this->assertEquals($response, $webResource->getResponse());
    }

    /**
     * @dataProvider createWithContentDataProvider
     *
     * @param InternetMediaTypeInterface|null $contentType
     *
     * @throws InvalidContentTypeException
     */
    public function testCreateWithContent(?InternetMediaTypeInterface $contentType)
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

    public function createWithContentDataProvider(): array
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
     * @dataProvider createWithResponseDataProvider
     *
     * @param string $responseContentType
     * @param string $expectedContentType
     * @param bool $expectedHasInvalidContentType
     *
     * @throws InvalidContentTypeException
     */
    public function testCreateWithResponse(
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

    public function createWithResponseDataProvider()
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
     * @throws UnwritableContentException
     */
    public function testSetContentForResourceWithResponseWithoutStreamFactory()
    {
        $contentTypeString = 'text/html';

        $responseBodyContent = 'response body content';

        $responseBody = \Mockery::mock(StreamInterface::class);
        $responseBody
            ->shouldReceive('__toString')
            ->andReturn($responseBodyContent);

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

        $this->expectException(UnwritableContentException::class);

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
