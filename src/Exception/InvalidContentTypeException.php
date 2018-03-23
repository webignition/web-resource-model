<?php

namespace webignition\WebResource\Exception;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
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
     * @param ResponseInterface $response
     * @param RequestInterface $request
     */
    public function __construct(
        InternetMediaTypeInterface $responseContentType,
        ResponseInterface $response = null,
        RequestInterface $request = null
    ) {
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
