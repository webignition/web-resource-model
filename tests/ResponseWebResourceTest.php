<?php

namespace webignition\Tests\WebResource;

use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use webignition\InternetMediaType\Parser\ParseException as InternetMediaTypeParseException;
use webignition\WebResource\Exception\ReadOnlyResponseException;
use webignition\WebResource\Exception\UnseekableResponseException;
use webignition\WebResource\ResponseWebResource;

class ResponseWebResourceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @throws InternetMediaTypeParseException
     */
    public function testCreate()
    {
        /* @var UriInterface|MockInterface $uri */
        $uri = \Mockery::mock(UriInterface::class);

        $contentTypeString = 'text/html';

        $responseBodyContent = 'response body content';

        $responseBody = \Mockery::mock(StreamInterface::class);
        $responseBody
            ->shouldReceive('__toString')
            ->andReturn($responseBodyContent);

        /* @var ResponseInterface|MockInterface $response */
        $response = \Mockery::mock(ResponseInterface::class);
        $response
            ->shouldReceive('getHeaderLine')
            ->with(ResponseWebResource::HEADER_CONTENT_TYPE)
            ->andReturn($contentTypeString);

        $response
            ->shouldReceive('getBody')
            ->andReturn($responseBody);

        $responseWebResource = new ResponseWebResource($uri, $response);

        $this->assertEquals($uri, $responseWebResource->getUri());
        $this->assertEquals($contentTypeString, (string)$responseWebResource->getContentType());
        $this->assertEquals($responseBodyContent, $responseWebResource->getContent());
        $this->assertEquals($responseBody, $responseWebResource->getBody());
        $this->assertEquals($response, $responseWebResource->getResponse());
    }

    /**
     * @throws InternetMediaTypeParseException
     * @throws ReadOnlyResponseException
     * @throws UnseekableResponseException
     */
    public function testSetContentForReadOnlyResource()
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
            ->with(ResponseWebResource::HEADER_CONTENT_TYPE)
            ->andReturn($contentTypeString);

        $response
            ->shouldReceive('getBody')
            ->andReturn($responseBody);

        $responseWebResource = new ResponseWebResource($uri, $response);

        $this->expectException(ReadOnlyResponseException::class);

        $responseWebResource->setContent('');
    }

    /**
     * @throws InternetMediaTypeParseException
     * @throws ReadOnlyResponseException
     * @throws UnseekableResponseException
     */
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
            ->with(ResponseWebResource::HEADER_CONTENT_TYPE)
            ->andReturn($contentTypeString);

        $response
            ->shouldReceive('getBody')
            ->andReturn($responseBody);

        $responseWebResource = new ResponseWebResource($uri, $response);

        $this->expectException(UnseekableResponseException::class);

        $responseWebResource->setContent('');
    }

    /**
     * @throws InternetMediaTypeParseException
     * @throws ReadOnlyResponseException
     * @throws UnseekableResponseException
     */
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
            ->with(ResponseWebResource::HEADER_CONTENT_TYPE)
            ->andReturn($contentTypeString);

        /* @var ResponseInterface|MockInterface $response */
        $response = \Mockery::mock(ResponseInterface::class);
        $response
            ->shouldReceive('getHeaderLine')
            ->with(ResponseWebResource::HEADER_CONTENT_TYPE)
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

        $responseWebResource = new ResponseWebResource($uri, $response);

        $updatedResponseWebResource = $responseWebResource->setContent($newResponseBodyContent);

        $this->assertNotEquals(spl_object_hash($updatedResponseWebResource), spl_object_hash($responseWebResource));
    }

    /**
     * @throws InternetMediaTypeParseException
     */
    public function testSetResponse()
    {
        /* @var UriInterface|MockInterface $uri */
        $uri = \Mockery::mock(UriInterface::class);

        $contentTypeString = 'text/html';

        $responseBodyContent = 'response body content';

        $responseBody = \Mockery::mock(StreamInterface::class);
        $responseBody
            ->shouldReceive('__toString')
            ->andReturn($responseBodyContent);

        /* @var ResponseInterface|MockInterface $response */
        $response = \Mockery::mock(ResponseInterface::class);
        $response
            ->shouldReceive('getHeaderLine')
            ->with(ResponseWebResource::HEADER_CONTENT_TYPE)
            ->andReturn($contentTypeString);

        $response
            ->shouldReceive('getBody')
            ->andReturn($responseBody);

        $responseWebResource = new ResponseWebResource($uri, $response);

        $this->assertEquals($responseBody, $responseWebResource->getBody());
        $this->assertEquals($response, $responseWebResource->getResponse());

        /* @var ResponseInterface|MockInterface $newResponse */
        $newResponse = \Mockery::mock(ResponseInterface::class);
        $newResponse
            ->shouldReceive('getHeaderLine')
            ->with(ResponseWebResource::HEADER_CONTENT_TYPE)
            ->andReturn($contentTypeString);

        $updatedResponseWebResource = $responseWebResource->setResponse($newResponse);

        $this->assertEquals($newResponse, $updatedResponseWebResource->getResponse());
    }

    /**
     * @throws InternetMediaTypeParseException
     */
    public function testSetBody()
    {
        /* @var UriInterface|MockInterface $uri */
        $uri = \Mockery::mock(UriInterface::class);

        $contentTypeString = 'text/html';

        $responseBodyContent = 'response body content';

        /* @var StreamInterface|MockInterface $responseBody */
        $responseBody = \Mockery::mock(StreamInterface::class);
        $responseBody
            ->shouldReceive('__toString')
            ->andReturn($responseBodyContent);

        /* @var StreamInterface|MockInterface $updatedResponseBody */
        $updatedResponseBody = \Mockery::mock(StreamInterface::class);

        /* @var ResponseInterface|MockInterface $updatedResponse */
        $updatedResponse = \Mockery::mock(ResponseInterface::class);
        $updatedResponse
            ->shouldReceive('getHeaderLine')
            ->with(ResponseWebResource::HEADER_CONTENT_TYPE)
            ->andReturn($contentTypeString);

        /* @var ResponseInterface|MockInterface $response */
        $response = \Mockery::mock(ResponseInterface::class);
        $response
            ->shouldReceive('getHeaderLine')
            ->with(ResponseWebResource::HEADER_CONTENT_TYPE)
            ->andReturn($contentTypeString);

        $response
            ->shouldReceive('getBody')
            ->andReturn($responseBody);

        $response
            ->shouldReceive('withBody')
            ->with($updatedResponseBody)
            ->andReturn($updatedResponse);

        $responseWebResource = new ResponseWebResource($uri, $response);

        $this->assertEquals($responseBody, $responseWebResource->getBody());
        $this->assertEquals($response, $responseWebResource->getResponse());

        $updatedResponseWebResource = $responseWebResource->setBody($updatedResponseBody);

        $this->assertNotEquals(spl_object_hash($updatedResponseWebResource), spl_object_hash($responseWebResource));
    }

    protected function tearDown()
    {
        parent::tearDown();

        \Mockery::close();
    }
}
