<?php

namespace webignition\Tests\WebResource\GetContent;

use webignition\Tests\WebResource\BaseTest;

class NoHttpResponseTest extends BaseTest {

    public function testResourceHasNullContent() {
        $this->assertNull($this->resource->getContent());
    }
}