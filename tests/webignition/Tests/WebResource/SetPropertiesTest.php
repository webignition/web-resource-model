<?php

namespace webignition\Tests\WebResource;

use \webignition\WebResource\WebResource;

class SetPropertiesTest extends BaseTest {

    public function testSetUrl() {
        $value = 'http://example.com/content';
        
        $resource = new \webignition\WebResource\WebResource();
        $resource->setUrl($value);
        
        $this->assertEquals($value, $resource->getUrl());
    }    
    
    public function testSetContentType() {
        $value = 'text/html; charset=utf-8';
        
        $resource = new \webignition\WebResource\WebResource();
        $resource->setContentType($value);
        
        $contentType = $resource->getContentType();
        $this->assertInstanceOf('\webignition\InternetMediaType\InternetMediaType', $contentType);        
        $this->assertEquals($value, (string)$resource->getContentType());
    }
    
    public function testSetContent() {
        $value = 'resource content here';
        
        $resource = new \webignition\WebResource\WebResource();
        $resource->setContent($value);
        
        $this->assertEquals($value, $resource->getContent());     
    }    
}