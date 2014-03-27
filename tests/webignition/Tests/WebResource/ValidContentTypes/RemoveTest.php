<?php

namespace webignition\Tests\WebResource\ValidContentTypes;

use webignition\InternetMediaType\InternetMediaType;

class RemoveTest extends ChangeTest {    
    
    public function testRemoveAll() {
        foreach ($this->resource->getValidContentTypes() as $mediaType) {
            $this->resource->removeValidContentType($mediaType);            
        }
        
        $this->assertEquals(0, count($this->resource->getValidContentTypes()));
    }
    
    
    public function testRemoveContentTypeThatInvalidatesHttpResponseContentTypeThrowsException() {
        $response = \Guzzle\Http\Message\Response::fromMessage("HTTP/1.0 200 OK\nContent-Type:foo0/bar");
        $this->resource->setHttpResponse($response);        
        
        $this->setExpectedException('webignition\WebResource\Exception', 'HTTP response contains invalid content type', 2);
        $this->resource->removeValidContentType($this->expectedMediaTypes['foo0/bar']);            
    }
    

}