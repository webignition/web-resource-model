<?php

namespace webignition\Tests\WebResource;

use Psr\Http\Message\ResponseInterface;
use webignition\InternetMediaType\Parser\ParseException as InternetMediaTypeParseException;
use webignition\Tests\WebResource\Factory\ResponseFactory;
use webignition\WebResource\Exception\InvalidContentTypeException;
use webignition\WebResource\SpecificContentTypeWebResource;

class SpecificContentTypeWebResourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider createValidContentTypeDataProvider
     *
     * @param ResponseInterface $response
     * @param string $expectedContentTypeString
     *
     * @throws InvalidContentTypeException
     * @throws InternetMediaTypeParseException
     */
    public function testCreateValidContentType(ResponseInterface $response, $expectedContentTypeString)
    {
        $webPage = new SpecificContentTypeWebResource($response);

        $this->assertEquals($expectedContentTypeString, (string)$webPage->getContentType());
    }

    /**
     * @return array
     */
    public function createValidContentTypeDataProvider()
    {
        return [
            'text/html' => [
                'response' => ResponseFactory::create('text/html'),
                'expectedContentTypeString' => 'text/html',
            ],
            'text/xml' => [
                'response' => ResponseFactory::create('text/xml'),
                'expectedContentTypeString' => 'text/xml',
            ],
            'application/xml' => [
                'response' => ResponseFactory::create('application/xml'),
                'expectedContentTypeString' => 'application/xml',
            ],
            'application/xhtml+xml' => [
                'response' => ResponseFactory::create('application/xhtml+xml'),
                'expectedContentTypeString' => 'application/xhtml+xml',
            ],
        ];
    }
}
