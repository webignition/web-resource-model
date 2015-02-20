<?php

namespace webignition\Tests\WebResource\SetContent;

use webignition\Tests\WebResource\ResponseBasedTest;

abstract class SetContentTest extends ResponseBasedTest {

    public function setUp() {
        parent::setUp();
        $this->resource->setContent($this->getNewContent());
    }

    abstract protected function getNewContent();

    public function testResponseContentMatchesExpectedNewContent() {
        $this->assertEquals($this->getNewContent(), $this->resource->getContent());
    }
}