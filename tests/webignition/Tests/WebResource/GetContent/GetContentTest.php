<?php

namespace webignition\Tests\WebResource\GetContent;

use webignition\Tests\WebResource\ResponseBasedTest;

abstract class GetContentTest extends ResponseBasedTest {

    abstract protected function getExpectedContent();

    public function testExpectedContentMatchesResponseContent() {
        $this->assertEquals($this->getExpectedContent(), $this->resource->getContent());
    }

}