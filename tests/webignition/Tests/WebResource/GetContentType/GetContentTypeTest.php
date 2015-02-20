<?php

namespace webignition\Tests\WebResource\GetContentType;

use webignition\Tests\WebResource\ResponseBasedTest;

abstract class GetContentTypeTest extends ResponseBasedTest {

    abstract protected function getExpectedContentType();

    public function testExpectedContentTypeMatchesResourceContentType() {
        $this->assertEquals($this->getExpectedContentType(), (string)$this->resource->getContentType());
    }
}