<?php

namespace webignition\Tests\WebResource\GetContentType;

class ResponseWithContentTypeTest extends GetContentTypeTest {

    protected function getHttpMessage() {
        return "HTTP/1.0 200 OK\nContent-Type:text/html; charset=utf-8";
    }

    function getExpectedContentType() {
        return 'text/html; charset=utf-8';
    }
}