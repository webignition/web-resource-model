<?php

namespace webignition\Tests\WebResource;

use GuzzleHttp\Message\ResponseInterface as HttpResponse;

abstract class ResponseBasedTest extends BaseTest {

    /**
     * @var HttpResponse
     */
    private $response;
    
    /**
     *
     * @var \webignition\WebResource\WebResource
     */
    protected $resource;
    
    public function setUp() {
        parent::setUp();

        $this->response = $this->getHttpResponseFromMessage($this->getHttpMessage());
        $this->resource->setHttpResponse($this->response);
    }

    abstract protected function getHttpMessage();
    
}