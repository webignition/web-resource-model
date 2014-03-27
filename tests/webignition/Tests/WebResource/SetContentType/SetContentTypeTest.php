<?php

namespace webignition\Tests\WebResource\SetContentType;

use webignition\Tests\WebResource\BaseTest;

abstract class SetContentTypeTest extends BaseTest {
    
    /**
     *
     * @var \webignition\WebResource\WebResource
     */
    protected $resource;
    
    public function setUp() {
        parent::setUp();
        
        $this->resource = new \webignition\WebResource\WebResource();
        
        foreach ($this->getValidContentTypes() as $contentType) {
            $this->resource->addValidContentType($contentType);
        } 
    }
    
    
    /**
     * @return \webignition\InternetMediaType\InternetMediaType[]
     */
    abstract protected function getValidContentTypes();  
}