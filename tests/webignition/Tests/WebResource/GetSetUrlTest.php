<?php

namespace webignition\Tests\WebResource;

class GetSetUrlTest extends BaseTest {
    
    public function testGetWithNoResponseUrlSet() {
        $response = \Guzzle\Http\Message\Response::fromMessage("HTTP/1.0 200 OK");        
        $this->resource->setHttpResponse($response);
        
        $this->assertNull($this->resource->getUrl());
    } 
    
    
    public function testGetWithResponseUrlSet() {
        $url = 'http://example.com/';
        
        $response = \Guzzle\Http\Message\Response::fromMessage("HTTP/1.0 200 OK\nContent-Type:text/html");        
        $response->setEffectiveUrl($url);
        $this->resource->setHttpResponse($response);        
        
        $this->assertEquals($url, $this->resource->getUrl());        
    }
    
    
    public function testSetUrlWithNoUrlPreviouslySet() {
        $url = 'http://example.com/';
        
        $response = \Guzzle\Http\Message\Response::fromMessage("HTTP/1.0 200 OK");        
        $this->resource->setHttpResponse($response);
        $this->resource->setUrl($url);
        
        $this->assertEquals($url, $this->resource->getUrl());        
    }
    

    public function testSetUrlWhenUrlPreviouslySet() {
        $url = 'http://example.com/';
        $newUrl = 'http://new.example.com/';
        
        $response = \Guzzle\Http\Message\Response::fromMessage("HTTP/1.0 200 OK");        
        $response->setEffectiveUrl($url);
        
        $this->resource->setHttpResponse($response);        
        $this->resource->setUrl($newUrl);
        
        $this->assertEquals($newUrl, $this->resource->getUrl());        
    } 
    
    
    public function testSetUrlBeforeSettingHttpResponse() {
        $url = 'http://example.com/';
        $this->assertEquals($url, $this->resource->setUrl($url)->getUrl());         
    }
}