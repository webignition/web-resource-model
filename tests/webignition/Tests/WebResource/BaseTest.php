<?php

namespace webignition\Tests\WebResource;

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
    
}