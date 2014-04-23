<?php

namespace webignition\Tests\WebResource\HandleInvalidContentType;

class DuplicatedTypeSubtypeTest extends HandleInvalidContentTypeTest {
       
    protected function getResponseContentType() {
        return 'text/html, text/html; charset=utf-8';
    }

    protected function getExpectedExtractedContentType() {
        return 'text/html; charset=utf-8';
    }
}