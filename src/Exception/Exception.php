<?php

namespace webignition\WebResource\Exception;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use webignition\WebResourceInterfaces\RetrieverExceptionInterface;

class Exception extends \Exception implements RetrieverExceptionInterface
{
    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param ResponseInterface $response
     * @param RequestInterface|null $request
     */
    public function __construct(ResponseInterface $response = null, RequestInterface $request = null)
    {
        $this->response = $response;
        $this->request = $request;

        $reasonPhrase = null;
        $statusCode = null;

        if (!empty($response)) {
            $reasonPhrase = $response->getReasonPhrase();
            $statusCode = $response->getStatusCode();
        }

        parent::__construct($reasonPhrase, $statusCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest()
    {
        return $this->request;
    }
}
