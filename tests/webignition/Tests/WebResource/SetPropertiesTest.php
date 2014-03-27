<?php

namespace webignition\Tests\WebResource;

class SetPropertiesTest extends BaseTest {

    public function testSetUrl() {
        $value = 'http://example.com/content';

        $this->resource->setUrl($value);
        
        $this->assertEquals($value, $this->resource->getUrl());
    }    
    
    public function testSetContentType() {
        $value = 'text/html; charset=utf-8';

        $this->resource->setContentType($value);

        $this->assertInstanceOf('\webignition\InternetMediaType\InternetMediaType', $this->resource->getContentType());        
        $this->assertEquals($value, (string)$this->resource->getContentType());
    }
    
    public function testSetContent() {
        $value = 'resource content here';
        
        $this->resource->setContent($value);
        
        $this->assertEquals($value, $this->resource->getContent());     
    }    
}