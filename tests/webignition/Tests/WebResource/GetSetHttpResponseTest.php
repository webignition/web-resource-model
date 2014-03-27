<?php

namespace webignition\Tests\WebResource;

use \webignition\InternetMediaType\InternetMediaType;

class GetSetHttpResponseTest extends BaseTest {
    
    public function testGetBeforeSetThrowsException() {
        $this->setExpectedException('webignition\WebResource\Exception', 'HTTP response not set', 1);
        $this->resource->getHttpResponse();
    }
 
    public function testSetReturnsSelf() {
        $response = \Guzzle\Http\Message\Response::fromMessage("HTTP/1.0 200 OK");        
        $this->assertEquals($this->resource, $this->resource->setHttpResponse($response));
    }    
    
    public function testGetReturnsThatSet() {
        $response = \Guzzle\Http\Message\Response::fromMessage("HTTP/1.0 200 OK"); 
        
        $this->assertEquals($response, $this->resource->setHttpResponse($response)->getHttpResponse());
    }
    
    public function testSetHttpResponseWithNoMediaTypeThrowsException() {                
        $this->setExpectedException('webignition\WebResource\Exception', 'HTTP response contains invalid media type', 2);
        
        $validMediaType = new InternetMediaType();
        $validMediaType->setType('foo');
        $validMediaType->setSubtype('bar');                
        
        $this->resource->addValidInternetMediaType($validMediaType);
        
        $response = \Guzzle\Http\Message\Response::fromMessage("HTTP/1.0 200 OK");        
        $this->assertEquals($this->resource, $this->resource->setHttpResponse($response));        
    }
    
    
    public function testSetHttpResponseWithInvalidMediaTypeThrowsException() {                
        $this->setExpectedException('webignition\WebResource\Exception', 'HTTP response contains invalid media type', 2);
        
        $validMediaType = new InternetMediaType();
        $validMediaType->setType('foo');
        $validMediaType->setSubtype('bar');                
        
        $this->resource->addValidInternetMediaType($validMediaType);
        
        $response = \Guzzle\Http\Message\Response::fromMessage("HTTP/1.0 200 OK\nContent-Type:text/html");        
        $this->assertEquals($this->resource, $this->resource->setHttpResponse($response));        
    }    
   
    
}