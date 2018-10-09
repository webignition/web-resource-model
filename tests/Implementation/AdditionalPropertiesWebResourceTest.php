<?php

namespace webignition\Tests\WebResource\Implementation;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use webignition\InternetMediaType\InternetMediaType;
use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;
use webignition\WebResource\Exception\InvalidContentTypeException;
use webignition\WebResource\WebResource;
use webignition\WebResource\WebResourceProperties;

class AdditionalPropertiesWebResourceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @throws InvalidContentTypeException
     */
    public function testCreateWithNoArguments()
    {
        $webResource = new AdditionalPropertiesWebResource();

        $this->assertNull($webResource->getUri());
        $this->assertNull($webResource->getContentType());
        $this->assertNull($webResource->getContent());
        $this->assertNull($webResource->getResponse());
        $this->assertNull($webResource->getType());
    }

    /**
     * @throws InvalidContentTypeException
     */
    public function testCreateFromContent()
    {
        $content = 'content';
        $contentType = new InternetMediaType('text', 'plain');
        $type = 'foo';

        $webResource = AdditionalPropertiesWebResource::createFromContent($content, $contentType, $type);

        $this->assertInstanceOf(AdditionalPropertiesWebResource::class, $webResource);
        $this->assertEquals($content, $webResource->getContent());
        $this->assertEquals($contentType, $webResource->getContentType());
        $this->assertNull($webResource->getUri());
        $this->assertNull($webResource->getResponse());
        $this->assertEquals($type, $webResource->getType());
    }

    /**
     * @throws InvalidContentTypeException
     */
    public function testCreateFromResponse()
    {
        $uri = \Mockery::mock(UriInterface::class);
        $contentTypeString = 'text/plain';
        $content = 'content';
        $type = 'foo';

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

        $webResource = AdditionalPropertiesWebResource::createFromResponse($uri, $response, $type);

        $this->assertInstanceOf(AdditionalPropertiesWebResource::class, $webResource);
        $this->assertEquals($content, $webResource->getContent());
        $this->assertEquals($contentTypeString, $webResource->getContentType());
        $this->assertEquals($uri, $webResource->getUri());
        $this->assertEquals($response, $webResource->getResponse());
        $this->assertEquals($type, $webResource->getType());
    }

    /**
     * @throws InvalidContentTypeException
     */
    public function testCreateWithContent()
    {
        $uri = \Mockery::mock(UriInterface::class);
        $contentType = \Mockery::mock(InternetMediaTypeInterface::class);
        $content = 'content';
        $type = 'foo';

        $webResource = new AdditionalPropertiesWebResource(AdditionalPropertiesWebResourceProperties::create([
            WebResourceProperties::ARG_URI => $uri,
            WebResourceProperties::ARG_CONTENT_TYPE => $contentType,
            WebResourceProperties::ARG_CONTENT => $content,
            AdditionalPropertiesWebResourceProperties::ARG_TYPE => $type,
        ]));

        $this->assertEquals($uri, $webResource->getUri());
        $this->assertEquals($contentType, $webResource->getContentType());
        $this->assertEquals($content, $webResource->getContent());
        $this->assertEquals($type, $webResource->getType());
    }

    /**
     * @throws InvalidContentTypeException
     */
    public function testCreateWithResponse()
    {
        $uri = \Mockery::mock(UriInterface::class);

        $type = 'foo';

        $responseContentType = 'foo/bar';
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

        $webResource = new AdditionalPropertiesWebResource(AdditionalPropertiesWebResourceProperties::create([
            WebResourceProperties::ARG_URI => $uri,
            WebResourceProperties::ARG_RESPONSE => $response,
            AdditionalPropertiesWebResourceProperties::ARG_TYPE => $type,
        ]));

        $this->assertEquals($uri, $webResource->getUri());
        $this->assertEquals($responseContentType, (string)$webResource->getContentType());
        $this->assertEquals($responseBodyContent, $webResource->getContent());
        $this->assertEquals($response, $webResource->getResponse());
        $this->assertEquals($type, $webResource->getType());
    }

    /**
     * @throws InvalidContentTypeException
     */
    public function testSetType()
    {
        $uri = \Mockery::mock(UriInterface::class);
        $contentType = \Mockery::mock(InternetMediaTypeInterface::class);
        $content = 'content';
        $currentType = 'foo';
        $newType = 'bar';

        $webResource = new AdditionalPropertiesWebResource(AdditionalPropertiesWebResourceProperties::create([
            WebResourceProperties::ARG_URI => $uri,
            WebResourceProperties::ARG_CONTENT_TYPE => $contentType,
            WebResourceProperties::ARG_CONTENT => $content,
            AdditionalPropertiesWebResourceProperties::ARG_TYPE => $currentType,
        ]));

        $this->assertEquals($uri, $webResource->getUri());
        $this->assertEquals($contentType, $webResource->getContentType());
        $this->assertEquals($content, $webResource->getContent());
        $this->assertEquals($currentType, $webResource->getType());

        $updatedWebResource = $webResource->setType($newType);

        $this->assertEquals($newType, $updatedWebResource->getType());
    }
}
