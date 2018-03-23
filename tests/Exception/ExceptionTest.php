<?php

namespace webignition\Tests\WebResource\Exception;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use webignition\WebResource\Exception\Exception;

class ExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getResponseGetRequestDataProvider
     *
     * @param ResponseInterface $response
     * @param RequestInterface $request
     * @param ResponseInterface|null $expectedResponse
     * @param RequestInterface|null $expectedRequest
     */
    public function testGetResponseGetRequest($response, $request, $expectedResponse, $expectedRequest)
    {
        $exception = new Exception($response, $request);

        $this->assertEquals($expectedResponse, $exception->getResponse());
        $this->assertEquals($expectedRequest, $exception->getRequest());
    }

    /**
     * @return array
     */
    public function getResponseGetRequestDataProvider()
    {
        $response = \Mockery::mock(ResponseInterface::class);
        $response
            ->shouldReceive('getReasonPhrase')
            ->andReturn('Not Found');
        $response
            ->shouldReceive('getStatusCode')
            ->andReturn(404);

        $request = \Mockery::mock(RequestInterface::class);

        return [
            'no response, no request' => [
                'response' => null,
                'request' => null,
                'expectedResponse' => null,
                'expectedRequest' => null,
            ],
            'has response, no request' => [
                'response' => $response,
                'request' => null,
                'expectedResponse' => $response,
                'expectedRequest' => null,
            ],
            'no response, has request' => [
                'response' => null,
                'request' => $request,
                'expectedResponse' => null,
                'expectedRequest' => $request,
            ],
            'has response, has request' => [
                'response' => $response,
                'request' => $request,
                'expectedResponse' => $response,
                'expectedRequest' => $request,
            ],
        ];
    }
}
