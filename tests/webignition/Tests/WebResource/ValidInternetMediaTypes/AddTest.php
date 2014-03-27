<?php

namespace webignition\Tests\WebResource\ValidInternetMediaTypes;

use webignition\InternetMediaType\InternetMediaType;

class AddTest extends ChangeTest {    
    
    public function testValidMediaTypeCountMatchesThatAdded() {
        $this->assertEquals(self::MEDIA_TYPE_COUNT, count($this->resource->getValidInternetMediaTypes()));
    }
    
    public function testValidMediaTypesMatchThoseAdded() {
        foreach ($this->resource->getValidInternetMediaTypes() as $index => $mediaType) {
            $this->assertEquals($this->expectedMediaTypes[$index], $mediaType);
        }
    }
    
    
    public function testAddMediaTypeThatInvalidatesHttpResponseMediaTypeThrowsException() {
        $this->setExpectedException('webignition\WebResource\Exception', 'HTTP response contains invalid media type', 2);
        
        foreach ($this->resource->getValidInternetMediaTypes() as $mediaType) {
            $this->resource->removeValidInternetMediaType($mediaType);            
        }
        
        $response = \Guzzle\Http\Message\Response::fromMessage("HTTP/1.0 200 OK");
        $this->resource->setHttpResponse($response);
        
        $mediaType = new InternetMediaType();
        $mediaType->setType('foo' . (self::MEDIA_TYPE_COUNT + 1));
        $mediaType->setSubtype('bar');        
        
        $this->resource->addValidInternetMediaType($mediaType);
    }
    

}