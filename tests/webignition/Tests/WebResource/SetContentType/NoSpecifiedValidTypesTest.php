<?php

namespace webignition\Tests\WebResource\SetContentType;

class NoSpecifiedValidTypesTest extends ValidContentTypeTest {
    
    protected function getContentTypeString() {
        return 'text/html; charset=utf-8';
    }

    protected function getValidContentTypes() {
        return array();
    }

}