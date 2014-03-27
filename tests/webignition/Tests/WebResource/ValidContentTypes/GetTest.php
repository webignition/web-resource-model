<?php

namespace webignition\Tests\WebResource\ValidContentTypes;

use webignition\Tests\WebResource\BaseTest;
use webignition\InternetMediaType\InternetMediaType;

class GetTest extends BaseTest {
    
    const MEDIA_TYPE_COUNT = 3;    
    
    public function testGetBeforeAddingReturnsEmptyCollection() {
        $this->assertEmpty($this->resource->getValidContentTypes());
    }

    public function testValidContentTypesMatchThoseAdded() {
        $expectedMediaTypes = array();
        
        for ($count = 0; $count < self::MEDIA_TYPE_COUNT; $count++) {
            $mediaType = new InternetMediaType();
            $mediaType->setType('foo' . $count);
            $mediaType->setSubtype('bar');
            
            $expectedMediaTypes[$mediaType->getTypeSubtypeString()] = $mediaType;
            $this->resource->addValidContentType($mediaType);
        }        
        
        foreach ($this->resource->getValidContentTypes() as $index => $mediaType) {
            $this->assertEquals($expectedMediaTypes[$index], $mediaType);
        }
    }
    

}