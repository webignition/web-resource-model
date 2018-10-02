<?php

namespace webignition\WebResource\Exception;

class MissingResponseException extends \Exception
{
    const MESSAGE = 'Response not set. Call setResponse() first.';
    const CODE = 1;

    public function __construct()
    {
        parent::__construct(self::MESSAGE, self::CODE);
    }
}
