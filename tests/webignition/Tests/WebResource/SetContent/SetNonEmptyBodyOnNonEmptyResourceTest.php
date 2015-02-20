<?php

namespace webignition\Tests\WebResource\SetContent;

class SetNonEmptyBodyOnNonEmptyResourceTest extends SetContentTest {

    protected function getHttpMessage() {
        return "HTTP/1.0 200 OK\n\nfoo";
    }


    protected function getNewContent() {
        return 'bar';
    }

}