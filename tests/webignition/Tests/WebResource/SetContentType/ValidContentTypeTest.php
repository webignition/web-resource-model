<?php

namespace webignition\Tests\WebResource\SetContentType;

abstract class ValidContentTypeTest extends SetContentTypeTest {
    
    public function setUp() {        
        parent::setUp();
        $this->resource->setContentType($this->getContentTypeString());    
    }
    
    
    /**
     * @return string
     */
    abstract protected function getContentTypeString();
    
    
    public function testResourceHasContentTypeObject() {
        $this->assertInstanceOf('\webignition\InternetMediaType\InternetMediaType', $this->resource->getContentType());
    }
    
    public function testResourceHasCorrectContentType() {
        $this->assertEquals($this->getContentTypeString(), (string)$this->resource->getContentType());
    }   
}