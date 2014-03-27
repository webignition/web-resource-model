<?php

namespace webignition\Tests\WebResource;

class GetSetHttpResponseHeadersTest extends BaseTest {

    public function testGetBeforeSetReturnsEmptyCollection() {
        $this->assertEquals(0, $this->resource->getHttpResponseHeaders()->count());
    }
    
    
    public function testSetReturnsSelf() {
        $this->assertEquals($this->resource, $this->resource->setHttpResponseHeaders(new \Guzzle\Http\Message\Header\HeaderCollection()));
    }
    
    
    public function testGetReturnsThatSet() {
        $headers = new \Guzzle\Http\Message\Header\HeaderCollection(array(
            'foo' => 'bar'
        ));
        
        $this->assertEquals($headers, $this->resource->setHttpResponseHeaders($headers)->getHttpResponseHeaders());
    }
   
    
}