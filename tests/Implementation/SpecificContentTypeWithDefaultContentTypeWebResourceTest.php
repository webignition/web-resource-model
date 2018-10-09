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
    public function testCreateFromContentWithNoContentType()
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
    public function testCreateFromContentWithCorrectContentType()
    {
        $content = 'resource content';

        $contentType = new InternetMediaType();
        $contentType->setType('foo');
        $contentType->setSubtype('bar');

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
    public function testCreateFromContentWithIncorrectContentType()
    {
        $content = 'resource content';

        $contentType = new InternetMediaType();
        $contentType->setType('invalid');
        $contentType->setSubtype('invalid');

        $this->expectException(InvalidContentTypeException::class);
        $this->expectExceptionMessage('Invalid content type "invalid/invalid"');

        new SpecificContentTypeWebResourceWithDefaultContentType(WebResourceProperties::create([
            WebResourceProperties::ARG_CONTENT => $content,
            WebResourceProperties::ARG_CONTENT_TYPE => $contentType,
        ]));
    }
}
