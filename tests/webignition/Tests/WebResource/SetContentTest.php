<?php

namespace webignition\Tests\WebResource;

class SetContentTest extends BaseTest {
    
    public function testSetEmptyBodyOnNonEmptyResource() {
        $content = '';
        
        $response = $this->getHttpResponseFromMessage("HTTP/1.0 200 OK\n\nfoo");
        $this->resource->setHttpResponse($response);
        $this->resource->setContent($content);
        $this->assertEquals($content, $this->resource->getContent());
    } 
    
    public function testSetNonEmptyBodyOnEmptyResource() {
        $content = 'foo';
        
        $response = $this->getHttpResponseFromMessage("HTTP/1.0 200 OK");
        $this->resource->setHttpResponse($response);
        $this->resource->setContent($content);
        $this->assertEquals($content, $this->resource->getContent());
    }     
    
    
    public function testSetEmptyBodyOnEmptyResource() {
        $content = '';
        
        $response = $this->getHttpResponseFromMessage("HTTP/1.0 200 OK");
        $this->resource->setHttpResponse($response);
        $this->resource->setContent($content);
        $this->assertEquals($content, $this->resource->getContent());       
    }
    
    public function testSetNonEmptyBodyOnNonEmptyResource() {
        $content = 'foo';
        
        $response = $this->getHttpResponseFromMessage("HTTP/1.0 200 OK\n\nbar");
        $this->resource->setHttpResponse($response);
        $this->resource->setContent($content);
        $this->assertEquals($content, $this->resource->getContent());       
    }    
}