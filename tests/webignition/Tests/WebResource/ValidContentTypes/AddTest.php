<?php

namespace webignition\Tests\WebResource\ValidContentTypes;

use webignition\InternetMediaType\InternetMediaType;

class AddTest extends ChangeTest {    
    
    public function testValidContentTypeCountMatchesThatAdded() {
        $this->assertEquals(self::MEDIA_TYPE_COUNT, count($this->resource->getValidContentTypes()));
    }
    
    public function testValidContentTypesMatchThoseAdded() {
        foreach ($this->resource->getValidContentTypes() as $index => $mediaType) {
            $this->assertEquals($this->expectedMediaTypes[$index], $mediaType);
        }
    }
    
    
    public function testAddContentTypeThatInvalidatesHttpResponseContentTypeThrowsException() {
        $this->setExpectedException('webignition\WebResource\Exception', 'HTTP response contains invalid content type', 2);
        
        foreach ($this->resource->getValidContentTypes() as $mediaType) {
            $this->resource->removeValidContentType($mediaType);            
        }
        
        $response = $this->getHttpResponseFromMessage("HTTP/1.0 200 OK");
        $this->resource->setHttpResponse($response);
        
        $mediaType = new InternetMediaType();
        $mediaType->setType('foo' . (self::MEDIA_TYPE_COUNT + 1));
        $mediaType->setSubtype('bar');        
        
        $this->resource->addValidContentType($mediaType);
    }
    

}