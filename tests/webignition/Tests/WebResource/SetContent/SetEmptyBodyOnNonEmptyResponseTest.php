<?php

namespace webignition\Tests\WebResource\SetContent;

class SetEmptyBodyOnNonEmptyResponseTest extends SetContentTest {

    protected function getHttpMessage() {
        return "HTTP/1.0 200 OK\n\nfoo";
    }


    protected function getNewContent() {
        return '';
    }

}