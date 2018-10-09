<?php

namespace webignition\Tests\WebResource\Implementation;

use webignition\InternetMediaType\InternetMediaType;
use webignition\WebResource\Exception\InvalidContentTypeException;
use webignition\WebResource\WebResourceProperties;

class SpecificContentTypeWithDefaultContentTypeWebResourceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @throws InvalidContentTypeException
     */
    public function testCreateWithNoArgs()
    {
        $webResource = new SpecificContentTypeWebResourceWithDefaultContentType();

        $this->assertEquals(
            SpecificContentTypeWebResourceWithDefaultContentType::getDefaultContentType(),
            $webResource->getContentType()
        );
    }

    /**
     * @throws InvalidContentTypeException
     */
    public function testCreateWithContentWithNoContentType()
    {
        $content = 'resource content';

        $webResource = new SpecificContentTypeWebResourceWithDefaultContentType(WebResourceProperties::create([
            WebResourceProperties::ARG_CONTENT => $content,
        ]));

        $this->assertEquals(
            SpecificContentTypeWebResourceWithDefaultContentType::getDefaultContentType(),
            $webResource->getContentType()
        );
        $this->assertEquals($content, $webResource->getContent());
        $this->assertNull($webResource->getResponse());
    }

    /**
     * @throws InvalidContentTypeException
     */
    public function testCreateWithContentWithCorrectContentType()
    {
        $content = 'resource content';
        $contentType = new InternetMediaType('foo', 'bar');

        $webResource = new SpecificContentTypeWebResourceWithDefaultContentType(WebResourceProperties::create([
            WebResourceProperties::ARG_CONTENT => $content,
            WebResourceProperties::ARG_CONTENT_TYPE => $contentType,
        ]));

        $this->assertEquals($contentType, $webResource->getContentType());
        $this->assertEquals($content, $webResource->getContent());
        $this->assertNull($webResource->getResponse());
    }

    /**
     * @throws InvalidContentTypeException
     */
    public function testCreateWithContentWithIncorrectContentType()
    {
        $content = 'resource content';
        $contentType = new InternetMediaType('invalid', 'invalid');

        $this->expectException(InvalidContentTypeException::class);
        $this->expectExceptionMessage('Invalid content type "invalid/invalid"');

        new SpecificContentTypeWebResourceWithDefaultContentType(WebResourceProperties::create([
            WebResourceProperties::ARG_CONTENT => $content,
            WebResourceProperties::ARG_CONTENT_TYPE => $contentType,
        ]));
    }
}
