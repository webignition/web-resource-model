<?php

namespace webignition\WebResource\Exception;

class UnseekableResponseException extends \Exception
{
    const MESSAGE = 'Response is unseekable';
    const CODE = 1;

    public function __construct()
    {
        parent::__construct(self::MESSAGE, self::CODE);
    }
}
