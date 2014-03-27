<?php

namespace webignition\Tests\WebResource\ValidInternetMediaTypes;

use webignition\Tests\WebResource\BaseTest;
use webignition\InternetMediaType\InternetMediaType;

abstract class ChangeTest extends BaseTest {
    
    const MEDIA_TYPE_COUNT = 3;
    
    protected $expectedMediaTypes = array();
    
    public function setUp() {
        parent::setUp();
        
        for ($count = 0; $count < self::MEDIA_TYPE_COUNT; $count++) {
            $mediaType = new InternetMediaType();
            $mediaType->setType('foo' . $count);
            $mediaType->setSubtype('bar');
            
            $this->expectedMediaTypes[$mediaType->getTypeSubtypeString()] = $mediaType;
            $this->resource->addValidInternetMediaType($mediaType);
        }
    }    

}