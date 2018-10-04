<?php

namespace webignition\Tests\WebResource\Implementation;

use Mockery\MockInterface;
use Psr\Http\Message\UriInterface;
use webignition\InternetMediaType\InternetMediaType;
use webignition\WebResource\Exception\InvalidContentTypeException;

class SpecificContentTypeWithDefaultContentTypeWebResourceTest extends \PHPUnit\Framework\TestCase
{
    public function testCreateFromContentWithNoContentType()
    {
        /* @var UriInterface|MockInterface $uri */
        $uri = \Mockery::mock(UriInterface::class);

        $content = 'resource content';

        $webResource = SpecificContentTypeWebResourceWithDefaultContentType::createFromContent($uri, $content, null);

        $this->assertEquals($uri, $webResource->getUri());
        $this->assertEquals(
            SpecificContentTypeWebResourceWithDefaultContentType::getDefaultContentType(),
            $webResource->getContentType()
        );
        $this->assertEquals($content, $webResource->getContent());
        $this->assertNull($webResource->getResponse());
    }

    public function testCreateFromContentWithCorrectContentType()
    {
        /* @var UriInterface|MockInterface $uri */
        $uri = \Mockery::mock(UriInterface::class);

        $content = 'resource content';

        $contentType = new InternetMediaType();
        $contentType->setType('foo');
        $contentType->setSubtype('bar');

        $webResource = SpecificContentTypeWebResourceWithDefaultContentType::createFromContent(
            $uri,
            $content,
            $contentType
        );

        $this->assertEquals($uri, $webResource->getUri());
        $this->assertEquals($contentType, $webResource->getContentType());
        $this->assertEquals($content, $webResource->getContent());
        $this->assertNull($webResource->getResponse());
    }

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

        SpecificContentTypeWebResourceWithDefaultContentType::createFromContent($uri, $content, $contentType);
    }
}
