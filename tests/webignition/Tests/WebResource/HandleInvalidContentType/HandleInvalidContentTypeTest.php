<?php

namespace webignition\Tests\WebResource\HandleInvalidContentType;

use webignition\Tests\WebResource\BaseTest;

abstract class HandleInvalidContentTypeTest extends BaseTest {
    
    private $extractedContentType;
    
    public function setUp() {
        parent::setUp();

        $response = \Guzzle\Http\Message\Response::fromMessage("HTTP/1.0 200 OK\nContent-Type:" . $this->getResponseContentType());        
        $this->resource->setHttpResponse($response);
        
        $this->extractedContentType = $this->resource->getContentType();      
    }
    
    abstract protected function getResponseContentType();
    abstract protected function getExpectedExtractedContentType();
    
    public function testExpectedExtractedContentTypeMatchesExtractedContentType() {
        $this->assertEquals($this->getExpectedExtractedContentType(), $this->extractedContentType);
    }
}