<?php

namespace webignition\WebResource\Exception;

use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;
use webignition\WebResourceInterfaces\InvalidContentTypeExceptionInterface;

class InvalidContentTypeException extends \Exception implements InvalidContentTypeExceptionInterface
{
    const MESSAGE = 'Invalid content type "%s"';
    const CODE = 0;

    /**
     * @var InternetMediaTypeInterface
     */
    private $responseContentType;

    public function __construct(InternetMediaTypeInterface $responseContentType)
    {
        parent::__construct(sprintf(self::MESSAGE, $responseContentType->getTypeSubtypeString()), self::CODE);

        $this->responseContentType = $responseContentType;
    }

    public function getContentType(): InternetMediaTypeInterface
    {
        return $this->responseContentType;
    }
}
