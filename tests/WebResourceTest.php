<?php

namespace webignition\Tests\WebResource;

use Mockery\MockInterface;
use Psr\Http\Message\UriInterface;
use webignition\InternetMediaType\InternetMediaType;
use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;
use webignition\WebResource\WebResource;

class WebResourceTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        /* @var UriInterface|MockInterface $uri */
        $uri = \Mockery::mock(UriInterface::class);

        /* @var InternetMediaTypeInterface|MockInterface $contentType */
        $contentType = \Mockery::mock(InternetMediaTypeInterface::class);

        $content = 'resource content';

        $webResource = new WebResource($uri, $contentType, $content);

        $this->assertEquals($uri, $webResource->getUri());
        $this->assertEquals($contentType, $webResource->getContentType());
        $this->assertEquals($content, $webResource->getContent());
    }

    public function testSetUri()
    {
        /* @var UriInterface|MockInterface $currentUri */
        $currentUri = \Mockery::mock(UriInterface::class);

        /* @var InternetMediaTypeInterface|MockInterface $contentType */
        $contentType = \Mockery::mock(InternetMediaTypeInterface::class);

        $content = 'resource content';

        $webResource = new WebResource($currentUri, $contentType, $content);

        /* @var UriInterface|MockInterface $newUri */
        $newUri = \Mockery::mock(UriInterface::class);

        $updatedWebResource = $webResource->setUri($newUri);

        $this->assertNotEquals(spl_object_hash($webResource), spl_object_hash($updatedWebResource));
        $this->assertEquals($currentUri, $webResource->getUri());
        $this->assertEquals($newUri, $updatedWebResource->getUri());
    }

    public function testSetContentType()
    {
        /* @var UriInterface|MockInterface $uri */
        $uri = \Mockery::mock(UriInterface::class);

        /* @var InternetMediaTypeInterface|MockInterface $currentContentType */
        $currentContentType = \Mockery::mock(InternetMediaTypeInterface::class);

        $content = 'resource content';

        $webResource = new WebResource($uri, $currentContentType, $content);

        /* @var InternetMediaTypeInterface|MockInterface $newContentType */
        $newContentType = \Mockery::mock(InternetMediaTypeInterface::class);

        $updatedWebResource = $webResource->setContentType($newContentType);

        $this->assertNotEquals(spl_object_hash($webResource), spl_object_hash($updatedWebResource));
        $this->assertEquals($currentContentType, $webResource->getContentType());
        $this->assertEquals($newContentType, $updatedWebResource->getContentType());
    }

    public function testSetContent()
    {
        /* @var UriInterface|MockInterface $uri */
        $uri = \Mockery::mock(UriInterface::class);

        /* @var InternetMediaTypeInterface|MockInterface $contentType */
        $contentType = \Mockery::mock(InternetMediaTypeInterface::class);

        $currentContent = 'resource content';

        $webResource = new WebResource($uri, $contentType, $currentContent);

        $newContent = 'updated resource content';

        $updatedWebResource = $webResource->setContent($newContent);

        $this->assertNotEquals(spl_object_hash($webResource), spl_object_hash($updatedWebResource));
        $this->assertEquals($currentContent, $webResource->getContent());
        $this->assertEquals($newContent, $updatedWebResource->getContent());
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

    public function testGetModelledContentTypeStrings()
    {
        $this->assertEquals([], WebResource::getModelledContentTypeStrings());
    }
}
