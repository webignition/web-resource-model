<?php

namespace webignition\Tests\WebResource;

class GetInternetMediaTypeTest extends BaseTest {
    
    public function testGetFromResponseWithNoContentType() {
        $response = \Guzzle\Http\Message\Response::fromMessage("HTTP/1.0 200 OK");        
        $this->resource->setHttpResponse($response);
        
        $this->assertEquals('', (string)$this->resource->getInternetMediaType());
    } 
    
    
    public function testGetFromResponseWithContentType() {
        $response = \Guzzle\Http\Message\Response::fromMessage("HTTP/1.0 200 OK\nContent-Type:text/html; charset=utf-8");        
        $this->resource->setHttpResponse($response);
        
        $this->assertEquals('text/html; charset=utf-8', (string)$this->resource->getInternetMediaType());        
    }
}