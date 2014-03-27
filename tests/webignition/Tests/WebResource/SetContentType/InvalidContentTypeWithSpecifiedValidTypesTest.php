<?php

namespace webignition\Tests\WebResource\SetContentType;

use webignition\InternetMediaType\InternetMediaType;

class InvalidContentTypeWithSpecifiedValidTypesTest extends InValidContentTypeTest {
    
    protected function getContentTypeString() {
        return 'text/plain; charset=utf-8';
    }    

    protected function getValidContentTypes() {
        $validContentType = new InternetMediaType();
        $validContentType->setType('text');
        $validContentType->setSubtype('html');
        
        return array($validContentType);
    }    
    
}