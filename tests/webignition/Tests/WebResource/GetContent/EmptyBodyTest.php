<?php

namespace webignition\Tests\WebResource\GetContent;

class EmptyBodyTest extends GetContentTest {

    protected function getHttpMessage() {
        return "HTTP/1.0 200 OK";
    }

    protected function getExpectedContent() {
        return '';
    }
}