<?php

namespace webignition\Tests\WebResource\Exception;

use Mockery\Mock;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;
use webignition\WebResource\Exception\InvalidContentTypeException;

class InvalidContentTypeExceptionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider getContentTypeDataProvider
     *
     * @param InternetMediaTypeInterface $responseContentType
     * @param string $expectedContentTypeString
     */
    public function testGetContentType($responseContentType, $expectedContentTypeString)
    {
        $response = \Mockery::mock(ResponseInterface::class);
        $response
            ->shouldReceive('getReasonPhrase')
            ->andReturn('OK');
        $response
            ->shouldReceive('getStatusCode')
            ->andReturn(200);

        $request = \Mockery::mock(RequestInterface::class);

        $exception = new InvalidContentTypeException($responseContentType, $response, $request);

        $this->assertEquals($expectedContentTypeString, (string)$exception->getContentType());
    }

    /**
     * @return array
     */
    public function getContentTypeDataProvider()
    {
        return [
            'default' => [
                'responseContentType' => $this->createContentType('text/plain'),
                'expectedContentTypeString' => 'text/plain',
            ],
        ];
    }

    /**
     * @param string $typeSubtypeString
     *
     * @return InternetMediaTypeInterface|Mock
     */
    private function createContentType($typeSubtypeString)
    {
        /* @var InternetMediaTypeInterface|Mock $contentType */
        $contentType = \Mockery::mock(InternetMediaTypeInterface::class);

        $contentType
            ->shouldReceive('getTypeSubtypeString')
            ->andReturn($typeSubtypeString);

        $contentType
            ->shouldReceive('__toString')
            ->andReturn($typeSubtypeString);

        return $contentType;
    }
}
