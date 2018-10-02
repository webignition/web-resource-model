<?php

namespace webignition\WebResource\Exception;

class ReadOnlyResponseException extends \Exception
{
    const MESSAGE = 'Response is read-only and cannot be written to';
    const CODE = 1;

    public function __construct()
    {
        parent::__construct(self::MESSAGE, self::CODE);
    }
}
