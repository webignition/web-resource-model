<?php

namespace webignition\Tests\WebResource;

use Mockery\Mock;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use webignition\InternetMediaType\InternetMediaType;
use webignition\WebResource\WebResource;

class WebResourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param ResponseInterface $response
     * @param string $url
     * @param string $expectedContentType
     * @param string $expectedContent
     */
    public function testCreate(ResponseInterface $response, $url, $expectedContentType, $expectedContent)
    {
        $resource = new WebResource($response, $url);

        $this->assertEquals($response, $resource->getResponse());
        $this->assertEquals($url, $resource->getUrl());

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
                'response' => $this->createResponse('text/plain', ''),
                'url' => null,
                'expectedContentType' => 'text/plain',
                'expectedContent' => '',
            ],
            'text/plain, empty content' => [
                'response' => $this->createResponse('text/plain', ''),
                'url' => 'http://example.com/foo.txt',
                'expectedContentType' => 'text/plain',
                'expectedContent' => '',
            ],
            'text/plain, non-empty content' => [
                'response' => $this->createResponse('text/plain', 'foo'),
                'url' => 'http://example.com/foo.txt',
                'expectedContentType' => 'text/plain',
                'expectedContent' => 'foo',
            ],
            'text/html, non-empty content' => [
                'response' => $this->createResponse('text/html', $htmlContent),
                'url' => 'http://example.com/foo.html',
                'expectedContentType' => 'text/html',
                'expectedContent' => $htmlContent,
            ],
            'text/html, gzipped content' => [
                'response' => $this->createResponse('text/html', gzencode($htmlContent)),
                'url' => 'http://example.com/foo.html',
                'expectedContentType' => 'text/html',
                'expectedContent' => $htmlContent,
            ],
            'text/html, bad gzipped content' => [
                'response' => $this->createResponse('text/html', 'foo'),
                'url' => 'http://example.com/foo.html',
                'expectedContentType' => 'text/html',
                'expectedContent' => 'foo',
            ],
        ];
    }

    /**
     * @param string $contentType
     * @param string $content
     *
     * @return Mock|ResponseInterface
     */
    private function createResponse($contentType, $content)
    {
        /* @var ResponseInterface|Mock $response */
        $response = \Mockery::mock(ResponseInterface::class);

        $response
            ->shouldReceive('getHeader')
            ->once()
            ->with(WebResource::HEADER_CONTENT_TYPE)
            ->andReturn([
                $contentType,
            ]);

        /* @var StreamInterface|Mock $bodyStream */
        $bodyStream = \Mockery::mock(StreamInterface::class);
        $bodyStream
            ->shouldReceive('__toString')
            ->once()
            ->withNoArgs()
            ->andReturn($content);

        $response
            ->shouldReceive('getBody')
            ->once()
            ->withNoArgs()
            ->andReturn($bodyStream);

        return $response;
    }
}
