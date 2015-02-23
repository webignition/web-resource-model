<?php

namespace webignition\Tests\WebResource\SetContent;

class SetEmptyBodyOnEmptyResourceTest extends SetContentTest {

    protected function getHttpMessage() {
        return "HTTP/1.0 200 OK";
    }


    protected function getNewContent() {
        return '';
    }

}