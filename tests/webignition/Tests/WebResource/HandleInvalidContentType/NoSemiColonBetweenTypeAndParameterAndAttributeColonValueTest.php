<?php

namespace webignition\Tests\WebResource\HandleInvalidContentType;

class NoSemiColonBetweenTypeAndParameterAndAttributeColonValueTest extends HandleInvalidContentTypeTest {
       
    protected function getResponseContentType() {
        return 'text/html charset: utf-8';
    }

    protected function getExpectedExtractedContentType() {
        return 'text/html; charset=utf-8';
    }
}