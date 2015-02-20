<?php

namespace webignition\Tests\WebResource\GetContent;

class NonEmptyBodyTest extends GetContentTest {

    protected function getHttpMessage() {
        return "HTTP/1.0 200 OK\n\nfoo";
    }

    protected function getExpectedContent() {
        return 'foo';
    }

}