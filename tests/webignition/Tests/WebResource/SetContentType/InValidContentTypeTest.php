<?php

namespace webignition\Tests\WebResource\SetContentType;

abstract class InValidContentTypeTest extends SetContentTypeTest {    
    
    public function testSetInvalidContentTypeThrowsWebResourceException() {
        try {
            $this->resource->setContentType($this->getContentTypeString());           
        } catch (\webignition\WebResource\Exception $exception) {
            $this->assertEquals(1, $exception->getCode());
            return;
        }
        
        $this->fail('Invalid content type exception not thrown');
    }
}