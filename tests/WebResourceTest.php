<?php

class WebResourceTest extends PHPUnit_Framework_TestCase {

    public function testSetGetUrl() {
        $value = 'http://example.com/content';
        
        $resource = new \webignition\WebResource\WebResource();
        $resource->setUrl($value);
        
        $this->assertEquals($value, $resource->getUrl());
    }    
    
    public function testSetGetContentType() {
        $value = 'text/html; charset=utf-8';
        
        $resource = new \webignition\WebResource\WebResource();
        $resource->setContentType($value);
        
        $this->assertEquals($value, $resource->getContentType());     
    }
    
    public function testSetGetContent() {
        $value = 'resource content here';
        
        $resource = new \webignition\WebResource\WebResource();
        $resource->setContent($value);
        
        $this->assertEquals($value, $resource->getContent());     
    }    
}