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

    /**
     * @param InternetMediaTypeInterface $responseContentType
     */
    public function __construct(InternetMediaTypeInterface $responseContentType)
    {
        parent::__construct(sprintf(self::MESSAGE, $responseContentType->getTypeSubtypeString()), self::CODE);

        $this->responseContentType = $responseContentType;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return $this->responseContentType;
    }
}
