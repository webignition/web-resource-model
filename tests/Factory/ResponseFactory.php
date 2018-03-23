<?php

namespace webignition\Tests\WebResource\Factory;

use Mockery;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use webignition\WebResource\WebResource;

class ResponseFactory
{
    /**
     * @param string $contentType
     * @param string $content
     * @param StreamInterface|null $bodyStream
     *
     * @return MockInterface|ResponseInterface
     */
    public static function create($contentType, $content = '', $bodyStream = null)
    {
        /* @var ResponseInterface|MockInterface $response */
        $response = Mockery::mock(ResponseInterface::class);

        $response
            ->shouldReceive('getHeader')
            ->with(WebResource::HEADER_CONTENT_TYPE)
            ->andReturn([
                $contentType,
            ]);

        if (empty($bodyStream)) {
            /* @var StreamInterface|MockInterface $bodyStream */
            $bodyStream = Mockery::mock(StreamInterface::class);
            $bodyStream
                ->shouldReceive('__toString')
                ->andReturn($content);
        }

        $response
            ->shouldReceive('getBody')
            ->andReturn($bodyStream);

        return $response;
    }
}
