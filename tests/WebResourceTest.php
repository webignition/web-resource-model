<?php

namespace webignition\Tests\WebResource;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use webignition\InternetMediaType\InternetMediaType;
use webignition\InternetMediaType\Parser\ParseException as InternetMediaTypeParseException;
use webignition\WebResource\TestingTools\ResponseFactory;
use webignition\WebResource\WebResource;
use webignition\WebResourceInterfaces\WebResourceInterface;

class WebResourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param ResponseInterface $response
     * @param string $expectedContentType
     * @param string $expectedContent
     *
     * @throws InternetMediaTypeParseException
     */
    public function testCreate(ResponseInterface $response, $expectedContentType, $expectedContent)
    {
        $uri = \Mockery::mock(UriInterface::class);
        $resource = new WebResource($response, $uri);

        $this->assertEquals($response, $resource->getResponse());
        $this->assertEquals($uri, $resource->getUri());

        $contentType = $resource->getContentType();
        $this->assertInstanceOf(InternetMediaType::class, $contentType);
        $this->assertEquals($expectedContentType, (string)$contentType);

        $this->assertEquals($expectedContent, $resource->getContent());
    }

    /**
     * @return array
     */
    public function createDataProvider()
    {
        $htmlContent = '<!doctype html><html></html>';

        return [
            'text/plain, empty content, no url' => [
                'response' => ResponseFactory::create('text/plain', ''),
                'expectedContentType' => 'text/plain',
                'expectedContent' => '',
            ],
            'text/plain, empty content' => [
                'response' => ResponseFactory::create('text/plain', ''),
                'expectedContentType' => 'text/plain',
                'expectedContent' => '',
            ],
            'text/plain, non-empty content' => [
                'response' => ResponseFactory::create('text/plain', 'foo'),
                'expectedContentType' => 'text/plain',
                'expectedContent' => 'foo',
            ],
            'text/html, non-empty content' => [
                'response' => ResponseFactory::create('text/html', $htmlContent),
                'expectedContentType' => 'text/html',
                'expectedContent' => $htmlContent,
            ],
            'text/html, gzipped content' => [
                'response' => ResponseFactory::create('text/html', gzencode($htmlContent)),
                'expectedContentType' => 'text/html',
                'expectedContent' => $htmlContent,
            ],
            'text/html, bad gzipped content' => [
                'response' => ResponseFactory::create('text/html', 'foo'),
                'expectedContentType' => 'text/html',
                'expectedContent' => 'foo',
            ],
        ];
    }

    /**
     * @dataProvider setResponseDataProvider
     *
     * @param WebResourceInterface $webResource
     * @param ResponseInterface $response
     * @param string $expectedResourceClassName
     */
    public function testSetResponse(
        WebResourceInterface $webResource,
        ResponseInterface $response,
        $expectedResourceClassName
    ) {
        $newWebResource = $webResource->setResponse($response);

        $this->assertNotEquals(spl_object_hash($webResource), spl_object_hash($newWebResource));

        $this->assertEquals($response, $newWebResource->getResponse());
        $this->assertEquals(spl_object_hash($webResource->getUri()), spl_object_hash($newWebResource->getUri()));
        $this->assertInstanceOf($expectedResourceClassName, $newWebResource);
    }

    /**
     * @return array
     *
     * @throws InternetMediaTypeParseException
     */
    public function setResponseDataProvider()
    {
        $uri = \Mockery::mock(UriInterface::class);
        $currentResponse = ResponseFactory::create('text/html');

        return [
            'WebResource instance' => [
                'webResource' => new WebResource($currentResponse, $uri),
                'response' => ResponseFactory::create('text/html'),
                'expectedResourceClassName' => WebResource::class,
            ],
        ];
    }

    /**
     * @dataProvider setUriDataProvider
     *
     * @param WebResourceInterface $webResource
     * @param UriInterface $uri
     * @param string $expectedResourceClassName
     */
    public function testSetUri(WebResourceInterface $webResource, UriInterface $uri, $expectedResourceClassName)
    {
        $newWebResource = $webResource->setUri($uri);

        $this->assertNotEquals(spl_object_hash($webResource), spl_object_hash($newWebResource));

        $this->assertEquals(
            spl_object_hash($webResource->getResponse()),
            spl_object_hash($newWebResource->getResponse())
        );
        $this->assertEquals($uri, $newWebResource->getUri());
        $this->assertInstanceOf($expectedResourceClassName, $newWebResource);
    }

    /**
     * @return array
     *
     * @throws InternetMediaTypeParseException
     */
    public function setUriDataProvider()
    {
        $response = ResponseFactory::create('text/html');
        $currentUri = \Mockery::mock(UriInterface::class);
        $newUri = \Mockery::mock(UriInterface::class);

        return [
            'WebResource instance' => [
                'webResource' => new WebResource($response, $currentUri),
                'uri' => $newUri,
                'expectedResourceClassName' => WebResource::class,
            ],
        ];
    }

    /**
     * @dataProvider setBodyDataProvider
     *
     * @param WebResourceInterface $webResource
     * @param StreamInterface $body
     * @param $expectedResourceClassName
     */
    public function testSetBody(WebResourceInterface $webResource, StreamInterface $body, $expectedResourceClassName)
    {
        $newWebResource = $webResource->setBody($body);

        $this->assertNotEquals(spl_object_hash($webResource), spl_object_hash($newWebResource));
        $this->assertNotEquals(
            spl_object_hash($webResource->getResponse()),
            spl_object_hash($newWebResource->getResponse())
        );

        $this->assertEquals($body, $newWebResource->getBody());

        $this->assertEquals(spl_object_hash($webResource->getUri()), spl_object_hash($newWebResource->getUri()));
        $this->assertInstanceOf($expectedResourceClassName, $newWebResource);
    }

    /**
     * @return array
     *
     * @throws InternetMediaTypeParseException
     */
    public function setBodyDataProvider()
    {
        $newBody = \Mockery::mock(StreamInterface::class);
        $newResponse = ResponseFactory::create('text/html', '', $newBody);

        $response = ResponseFactory::create('text/html');
        $response
            ->shouldReceive('withBody')
            ->andReturn($newResponse);

        $uri = \Mockery::mock(UriInterface::class);

        return [
            'WebResource instance' => [
                'webResource' => new WebResource($response, $uri),
                'body' => $newBody,
                'expectedResourceClassName' => WebResource::class,
            ],
        ];
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
}
