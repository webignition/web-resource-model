<?php

namespace webignition\Tests\WebResource\SetContentType;

use webignition\InternetMediaType\InternetMediaType;

class SpecifiedValidTypesTest extends ValidContentTypeTest {
    
    protected function getContentTypeString() {
        return 'text/html; charset=utf-8';
    }    

    protected function getValidContentTypes() {
        $validContentType = new InternetMediaType();
        $validContentType->setType('text');
        $validContentType->setSubtype('html');
        
        return array($validContentType);
    }    
    
}