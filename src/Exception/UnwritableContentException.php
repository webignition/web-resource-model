<?php

namespace webignition\WebResource\Exception;

use Psr\Http\Message\StreamInterface;
use webignition\StreamFactoryInterface\StreamFactoryInterface;

class UnwritableContentException extends \Exception
{
    const MESSAGE = 'Unable to write to a response with a response. Pass a %s implementation into setContent()'
                   .' as the second argument to allow a %s instance to be created.';
    const CODE = 0;

    public static function create(): UnwritableContentException
    {
        return new UnwritableContentException(
            sprintf(
                self::MESSAGE,
                StreamFactoryInterface::class,
                StreamInterface::class
            ),
            self::CODE
        );
    }
}
