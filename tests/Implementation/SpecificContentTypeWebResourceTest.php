<?php

namespace webignition\Tests\WebResource\Implementation;

use Mockery\MockInterface;
use Psr\Http\Message\UriInterface;
use webignition\InternetMediaType\InternetMediaType;
use webignition\WebResource\Exception\InvalidContentTypeException;

class SpecificContentTypeWebResourceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @throws InvalidContentTypeException
     */
    public function testCreateFromContentWithNoContentType()
    {
        /* @var UriInterface|MockInterface $uri */
        $uri = \Mockery::mock(UriInterface::class);

        $content = 'resource content';

        $webResource = SpecificContentTypeWebResource::createFromContent($uri, $content, null);

        $this->assertEquals($uri, $webResource->getUri());
        $this->assertEquals(null, $webResource->getContentType());
        $this->assertEquals($content, $webResource->getContent());
        $this->assertNull($webResource->getResponse());
    }

    /**
     * @throws InvalidContentTypeException
     */
    public function testCreateFromContentWithCorrectContentType()
    {
        /* @var UriInterface|MockInterface $uri */
        $uri = \Mockery::mock(UriInterface::class);

        $content = 'resource content';

        $contentType = new InternetMediaType();
        $contentType->setType('foo');
        $contentType->setSubtype('bar');

        $webResource = SpecificContentTypeWebResource::createFromContent($uri, $content, $contentType);

        $this->assertEquals($uri, $webResource->getUri());
        $this->assertEquals($contentType, $webResource->getContentType());
        $this->assertEquals($content, $webResource->getContent());
        $this->assertNull($webResource->getResponse());
    }

    /**
     * @throws InvalidContentTypeException
     */
    public function testCreateFromContentWithIncorrectContentType()
    {
        /* @var UriInterface|MockInterface $uri */
        $uri = \Mockery::mock(UriInterface::class);

        $content = 'resource content';

        $contentType = new InternetMediaType();
        $contentType->setType('invalid');
        $contentType->setSubtype('invalid');

        $this->expectException(InvalidContentTypeException::class);
        $this->expectExceptionMessage('Invalid content type "invalid/invalid"');

        SpecificContentTypeWebResource::createFromContent($uri, $content, $contentType);
    }
}
