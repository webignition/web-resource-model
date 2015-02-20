<?php

namespace webignition\Tests\WebResource;

use GuzzleHttp\Message\MessageFactory as HttpMessageFactory;
use GuzzleHttp\Message\ResponseInterface as HttpResponse;

abstract class BaseTest extends \PHPUnit_Framework_TestCase {   
    
    /**
     *
     * @var \webignition\WebResource\WebResource
     */
    protected $resource;
    
    public function setUp() {
        parent::setUp();
        
        $this->resource = new \webignition\WebResource\WebResource();
    }


    /**
     * @param $message
     * @return HttpResponse
     */
    protected function getHttpResponseFromMessage($message) {
        $factory = new HttpMessageFactory();
        return $factory->fromMessage($message);
    }
    
}