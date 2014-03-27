<?php

namespace webignition\Tests\WebResource;

class GetUrlTest extends BaseTest {
    
    public function testGetWithNoResponseUrlSet() {
        $response = \Guzzle\Http\Message\Response::fromMessage("HTTP/1.0 200 OK");        
        $this->resource->setHttpResponse($response);
        
        $this->assertNull($this->resource->getUrl());
    } 
    
    
    public function tsetGetWithResponseUrlSet() {
        $url = 'http://example.com/';
        
        $response = \Guzzle\Http\Message\Response::fromMessage("HTTP/1.0 200 OK\nContent-Type:text/html");        
        $response->setEffectiveUrl($url);
        $this->resource->setHttpResponse($response);        
        
        $this->assertEquals($url, $this->resource->getUrl());        
    }
}