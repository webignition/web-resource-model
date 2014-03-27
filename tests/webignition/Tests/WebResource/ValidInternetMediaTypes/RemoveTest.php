<?php

namespace webignition\Tests\WebResource\ValidInternetMediaTypes;

use webignition\InternetMediaType\InternetMediaType;

class RemoveTest extends ChangeTest {    
    
    public function testRemoveAll() {
        foreach ($this->resource->getValidInternetMediaTypes() as $mediaType) {
            $this->resource->removeValidInternetMediaType($mediaType);            
        }
        
        $this->assertEquals(0, count($this->resource->getValidInternetMediaTypes()));
    }
    
    
    public function testRemoveMediaTypeThatInvalidatesHttpResponseMediaTypeThrowsException() {
        $response = \Guzzle\Http\Message\Response::fromMessage("HTTP/1.0 200 OK\nContent-Type:foo0/bar");
        $this->resource->setHttpResponse($response);        
        
        $this->setExpectedException('webignition\WebResource\Exception', 'HTTP response contains invalid media type', 2);
        $this->resource->removeValidInternetMediaType($this->expectedMediaTypes['foo0/bar']);            
    }
    

}