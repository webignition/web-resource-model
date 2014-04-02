<?php

namespace webignition\Tests\WebResource;

class GetContentTest extends BaseTest {
    
    public function testGetWithEmptyBody() {
        $response = \Guzzle\Http\Message\Response::fromMessage("HTTP/1.0 200 OK");        
        $this->resource->setHttpResponse($response);
        $this->assertEquals('', $this->resource->getContent());
    } 
    
    
    public function testGetWithNonEmptyBody() {
        $content = 'foo';
        
        $response = \Guzzle\Http\Message\Response::fromMessage("HTTP/1.0 200 OK\nContent-Type:text/html\n\n" . $content);        
        $this->resource->setHttpResponse($response);
        
        $this->assertEquals($content, $this->resource->getContent());        
    }
    
    public function testGetWithNoHttpResponseReturnsNull() {
        $this->assertNull($this->resource->getContent());
    }
}