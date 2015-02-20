<?php

namespace webignition\Tests\WebResource;

class GetContentTypeTest extends BaseTest {
    
    public function testGetFromResponseWithNoContentType() {
        $response = $this->getHttpResponseFromMessage("HTTP/1.0 200 OK");
        $this->resource->setHttpResponse($response);

        $this->assertEquals('', (string)$this->resource->getContentType());
    }
    
    
    public function testGetFromResponseWithContentType() {
        $response = $this->getHttpResponseFromMessage("HTTP/1.0 200 OK\nContent-Type:text/html; charset=utf-8");
        $this->resource->setHttpResponse($response);
        
        $this->assertEquals('text/html; charset=utf-8', (string)$this->resource->getContentType());        
    }
}