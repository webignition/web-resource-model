<?php

namespace webignition\Tests\WebResource;

use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use webignition\InternetMediaType\InternetMediaType;
use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;
use webignition\WebResource\Exception\InvalidContentTypeException;
use webignition\WebResource\WebResource;
use webignition\WebResource\WebResourceProperties;
use webignition\WebResourceInterfaces\WebResourceInterface;

class WebResourceMutationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var WebResourceInterface
     */
    private $webResource;

    /**
     * @var WebResourceInterface
     */
    private $updatedWebResource;

    protected function assertPostConditions()
    {
        parent::assertPostConditions();

        $this->assertInstanceOf(WebResourceInterface::class, $this->webResource);
        $this->assertInstanceOf(WebResourceInterface::class, $this->updatedWebResource);
        $this->assertNotEquals(spl_object_hash($this->webResource), spl_object_hash($this->updatedWebResource));
    }

    /**
     * @throws InvalidContentTypeException
     */
    public function testSetUri()
    {
        $currentUri = \Mockery::mock(UriInterface::class);
        $content = 'content';
        $contentType = \Mockery::mock(InternetMediaTypeInterface::class);

        $this->createWebResource([
            WebResourceProperties::ARG_URI => $currentUri,
            WebResourceProperties::ARG_CONTENT_TYPE => $contentType,
            WebResourceProperties::ARG_CONTENT => $content,
        ]);

        $this->assertEquals($currentUri, $this->webResource->getUri());
        $this->assertEquals($content, $this->webResource->getContent());
        $this->assertEquals($contentType, $this->webResource->getContentType());
        $this->assertNull($this->webResource->getResponse());

        $newUri = \Mockery::mock(UriInterface::class);

        $this->updatedWebResource = $this->webResource->setUri($newUri);

        $this->assertEquals($currentUri, $this->webResource->getUri());
        $this->assertEquals($content, $this->webResource->getContent());
        $this->assertEquals($contentType, $this->webResource->getContentType());
        $this->assertNull($this->webResource->getResponse());

        $this->assertEquals($newUri, $this->updatedWebResource->getUri());
        $this->assertEquals($content, $this->updatedWebResource->getContent());
        $this->assertEquals($contentType, $this->updatedWebResource->getContentType());
        $this->assertNull($this->updatedWebResource->getResponse());
    }

    /**
     * @throws InvalidContentTypeException
     */
    public function testSetContentTypeForResourceWithoutResponse()
    {
        $uri = \Mockery::mock(UriInterface::class);
        $content = 'content';
        $currentContentType = \Mockery::mock(InternetMediaTypeInterface::class);

        $this->createWebResource([
            WebResourceProperties::ARG_URI => $uri,
            WebResourceProperties::ARG_CONTENT_TYPE => $currentContentType,
            WebResourceProperties::ARG_CONTENT => $content,
        ]);

        $this->assertEquals($uri, $this->webResource->getUri());
        $this->assertEquals($content, $this->webResource->getContent());
        $this->assertEquals($currentContentType, $this->webResource->getContentType());
        $this->assertNull($this->webResource->getResponse());

        $newContentType = \Mockery::mock(InternetMediaTypeInterface::class);

        $this->updatedWebResource = $this->webResource->setContentType($newContentType);

        $this->assertEquals($uri, $this->webResource->getUri());
        $this->assertEquals($content, $this->webResource->getContent());
        $this->assertEquals($currentContentType, $this->webResource->getContentType());
        $this->assertNull($this->webResource->getResponse());

        $this->assertEquals($uri, $this->updatedWebResource->getUri());
        $this->assertEquals($content, $this->updatedWebResource->getContent());
        $this->assertEquals($newContentType, $this->updatedWebResource->getContentType());
        $this->assertNull($this->updatedWebResource->getResponse());
    }

    /**
     * @throws InvalidContentTypeException
     */
    public function testSetContentTypeForResourceWithResponse()
    {
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

        $this->createWebResource([
            WebResourceProperties::ARG_URI => $uri,
            WebResourceProperties::ARG_RESPONSE => $response,
        ]);

        $this->assertEquals($uri, $this->webResource->getUri());
        $this->assertEquals($responseContentType, (string)$this->webResource->getContentType());
        $this->assertEquals($response, $this->webResource->getResponse());

        $this->updatedWebResource = $this->webResource->setContentType($updatedContentType);

        $this->assertEquals($responseContentType, (string)$this->webResource->getContentType());
        $this->assertEquals($response, $this->webResource->getResponse());

        $this->assertEquals($uri, $this->updatedWebResource->getUri());
        $this->assertEquals($updatedResponse, $this->updatedWebResource->getResponse());
        $this->assertEquals($updatedContentTypeString, (string)$this->updatedWebResource->getContentType());
    }

    /**
     * @throws InvalidContentTypeException
     */
    public function testSetContentForResourceWithoutResponse()
    {
        $uri = \Mockery::mock(UriInterface::class);
        $currentContent = 'resource content';
        $contentType = \Mockery::mock(InternetMediaTypeInterface::class);

        $this->createWebResource([
            WebResourceProperties::ARG_URI => \Mockery::mock(UriInterface::class),
            WebResourceProperties::ARG_CONTENT => $currentContent,
            WebResourceProperties::ARG_CONTENT_TYPE => $contentType,
        ]);

        $this->assertEquals($uri, $this->webResource->getUri());
        $this->assertEquals($currentContent, $this->webResource->getContent());
        $this->assertEquals($contentType, $this->webResource->getContentType());
        $this->assertNull($this->webResource->getResponse());

        $newContent = 'updated resource content';

        $this->updatedWebResource = $this->webResource->setContent($newContent);

        $this->assertEquals($uri, $this->webResource->getUri());
        $this->assertEquals($currentContent, $this->webResource->getContent());
        $this->assertEquals($contentType, $this->webResource->getContentType());
        $this->assertNull($this->webResource->getResponse());

        $this->assertEquals($uri, $this->updatedWebResource->getUri());
        $this->assertEquals($newContent, $this->updatedWebResource->getContent());
        $this->assertEquals($contentType, $this->updatedWebResource->getContentType());
        $this->assertNull($this->updatedWebResource->getResponse());
    }

    /**
     * @throws InvalidContentTypeException
     */
    public function testSetContentForResourceWithResponse()
    {
        $uri = \Mockery::mock(UriInterface::class);

        $contentTypeString = 'text/html';

        $responseBodyContent = 'response body content';
        $newResponseBodyContent = 'new response body content';

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

        $newResponse = \Mockery::mock(ResponseInterface::class);
        $newResponse
            ->shouldReceive('getHeaderLine')
            ->with(WebResource::HEADER_CONTENT_TYPE)
            ->andReturn($contentTypeString);

        $newResponse
            ->shouldReceive('getBody')
            ->andReturn($newResponseBodyContent);

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

        $this->createWebResource([
            WebResourceProperties::ARG_URI => $uri,
            WebResourceProperties::ARG_RESPONSE => $response,
        ]);

        $this->assertEquals($uri, $this->webResource->getUri());
        $this->assertEquals($response, $this->webResource->getResponse());
        $this->assertEquals($contentTypeString, (string)$this->webResource->getContentType());
        $this->assertEquals($responseBodyContent, $this->webResource->getContent());

        $this->updatedWebResource = $this->webResource->setContent($newResponseBodyContent);

        $this->assertEquals($uri, $this->webResource->getUri());
        $this->assertEquals($response, $this->webResource->getResponse());
        $this->assertEquals($contentTypeString, (string)$this->webResource->getContentType());
        $this->assertEquals($responseBodyContent, $this->webResource->getContent());

        $this->assertEquals($uri, $this->updatedWebResource->getUri());
        $this->assertEquals($newResponse, $this->updatedWebResource->getResponse());
        $this->assertEquals($contentTypeString, (string)$this->updatedWebResource->getContentType());
        $this->assertEquals($newResponseBodyContent, $this->updatedWebResource->getContent());
    }

    /**
     * @throws InvalidContentTypeException
     */
    public function testSetResponseForResourceWithoutResponse()
    {
        $uri = \Mockery::mock(UriInterface::class);

        $content = 'content';
        $resourceContent = 'resource content';

        $contentTypeString = 'text/html';
        $contentType = \Mockery::mock(InternetMediaTypeInterface::class);
        $contentType
            ->shouldReceive('__toString')
            ->andReturn($contentTypeString);

        $this->createWebResource([
            WebResourceProperties::ARG_URI => $uri,
            WebResourceProperties::ARG_CONTENT => $content,
            WebResourceProperties::ARG_CONTENT_TYPE => $contentType,
        ]);

        $this->assertEquals($uri, $this->webResource->getUri());
        $this->assertEquals($content, $this->webResource->getContent());
        $this->assertNull($this->webResource->getResponse());

        $responseBody = \Mockery::mock(StreamInterface::class);
        $responseBody
            ->shouldReceive('__toString')
            ->andReturn($resourceContent);

        /* @var ResponseInterface|MockInterface $response */
        $response = \Mockery::mock(ResponseInterface::class);
        $response
            ->shouldReceive('getHeaderLine')
            ->with(WebResource::HEADER_CONTENT_TYPE)
            ->andReturn($contentTypeString);

        $response
            ->shouldReceive('getBody')
            ->andReturn($responseBody);

        $this->updatedWebResource = $this->webResource->setResponse($response);

        $this->assertEquals($uri, $this->webResource->getUri());
        $this->assertEquals($content, $this->webResource->getContent());
        $this->assertEquals($contentTypeString, (string)$this->webResource->getContentType());
        $this->assertNull($this->webResource->getResponse());

        $this->assertEquals($uri, $this->updatedWebResource->getUri());
        $this->assertEquals($response, $this->updatedWebResource->getResponse());
        $this->assertEquals($contentTypeString, (string)$this->updatedWebResource->getContentType());
        $this->assertEquals($resourceContent, $this->updatedWebResource->getContent());
    }

    /**
     * @param array $args
     *
     * @throws InvalidContentTypeException
     */
    private function createWebResource(array $args)
    {
        $this->webResource = new WebResource(WebResourceProperties::create($args));
    }
}
