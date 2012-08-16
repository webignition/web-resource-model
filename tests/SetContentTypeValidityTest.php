<?php

use webignition\WebResource\WebResource;
use webignition\InternetMediaType\InternetMediaType;

class SetContentTypeValidityTest extends PHPUnit_Framework_TestCase {
    
    public function testSetContentTypeWithNoSpecifiedValidTypes() {
        $value = 'text/html; charset=utf-8';
        
        $resource = new \webignition\WebResource\WebResource();
        $resource->setContentType($value);
        
        $contentType = $resource->getContentType();
        $this->assertInstanceOf('\webignition\InternetMediaType\InternetMediaType', $contentType);        
        $this->assertEquals($value, (string)$resource->getContentType());
    } 
    
 
    public function testSetValidContentTypeWithSpecifiedValidTypes() {
        $value = 'text/html; charset=utf-8';
        
        $validContentType = new InternetMediaType();
        $validContentType->setType('text');
        $validContentType->setSubtype('html');
        
        $resource = new \webignition\WebResource\WebResource();
        $resource->addValidContentType($validContentType);
        $resource->setContentType($value);
        
        $contentType = $resource->getContentType();
        $this->assertInstanceOf('\webignition\InternetMediaType\InternetMediaType', $contentType);        
        $this->assertEquals($value, (string)$resource->getContentType());
    } 
    
    
    public function testSetInvalidContentTypeWithSpecifiedValidTypes() {
        $value = 'text/plain; charset=utf-8';
        
        $validContentType = new InternetMediaType();
        $validContentType->setType('text');
        $validContentType->setSubtype('html');
        
        $resource = new \webignition\WebResource\WebResource();
        $resource->addValidContentType($validContentType);
        
        try {
            $resource->setContentType($value);            
        } catch (\webignition\WebResource\Exception $exception) {
            $this->assertEquals(1, $exception->getCode());
            return;
        }
        
        $this->fail('Invalid content type exception not thrown');
    }    
}