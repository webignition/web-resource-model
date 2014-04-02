<?php

namespace webignition\Tests\WebResource\GetContent;

use webignition\Tests\WebResource\BaseTest;

class GzipTest extends BaseTest {
    
    public function testGetContentFromGzipResponse() {        
        $response = \Guzzle\Http\Message\Response::fromMessage(file_get_contents(__DIR__ . '/gzipResponse.txt'));        
        $this->resource->setHttpResponse($response);
        
        $this->assertRegExp('/^<\?xml/', $this->resource->getContent());
    }
}