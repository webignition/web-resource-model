<?php

namespace webignition\Tests\WebResource\GetContent;

use webignition\Tests\WebResource\ResponseBasedTest;

class GzipTest extends ResponseBasedTest {

    protected function getHttpMessage() {
        return $this->getHttpResponseFromMessage(file_get_contents(__DIR__ . '/gzipResponse.txt'));
    }

    
    public function testGetContentFromGzipResponse() {
        $this->assertRegExp('/^<\?xml/', $this->resource->getContent());
    }
}