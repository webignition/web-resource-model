<?php

namespace webignition\Tests\WebResource\SetContentType;

use webignition\Tests\WebResource\BaseTest;

abstract class SetContentTypeTest extends BaseTest {
    
    public function setUp() {
        parent::setUp();
        
        foreach ($this->getValidContentTypes() as $contentType) {
            $this->resource->addValidContentType($contentType);
        } 
    }
    
    
    /**
     * @return \webignition\InternetMediaType\InternetMediaType[]
     */
    abstract protected function getValidContentTypes();  
}