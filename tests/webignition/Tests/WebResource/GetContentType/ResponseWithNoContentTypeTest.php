<?php

namespace webignition\Tests\WebResource\GetContentType;

class ResponseWithNoContentTypeTest extends GetContentTypeTest {

    protected function getHttpMessage() {
        return "HTTP/1.0 200 OK";
    }

    function getExpectedContentType() {
        return '';
    }

}