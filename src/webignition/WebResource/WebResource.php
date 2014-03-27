<?php
namespace webignition\WebResource;

use \webignition\InternetMediaType\InternetMediaType;

/**
 * Models a web-based resource by providing access to commonly-used aspects
 * of a HTTP response
 */
class WebResource
{    
    /**
     *
     * @var \Guzzle\Http\Message\Response
     */
    private $httpResponse;
   
    
    /**
     *
     * @var InternetMediaType
     */
    private $internetMediaType;
    
    
    /**
     *  Collection of valid internet media type types and subtypes allowed for
     *  this resource
     * 
     * @var InternetMediaType[]
     */
    private $validInternetMediaTypes = array();

    
    /**
     *
     * @param InternetMediaType $mediaType
     * @return \webignition\WebResource\WebResource 
     */
    public function addValidInternetMediaType(InternetMediaType $mediaType) {
        $addedMediaType = new InternetMediaType();
        $addedMediaType->setType($mediaType->getType());
        $addedMediaType->setSubtype($mediaType->getSubtype());
        
        $this->validInternetMediaTypes[$addedMediaType->getTypeSubtypeString()] = $addedMediaType;
        
        if ($this->hasHttpResponse() && !$this->hasValidInternetMediaType()) {
            throw new Exception('HTTP response contains invalid media type', 2);
        }        
        
        return $this;        
    }
    
    
    /**
     *
     * @param InternetMediaType $mediaType
     * @return \webignition\WebResource\WebResource 
     */
    public function removeValidInternetMediaType(InternetMediaType $mediaType) {
        if (array_key_exists($mediaType->getTypeSubtypeString(), $this->validInternetMediaTypes)) {
            unset($this->validInternetMediaTypes[$mediaType->getTypeSubtypeString()]);
        }
        
        if ($this->hasHttpResponse() && !$this->hasValidInternetMediaType()) {
            throw new Exception('HTTP response contains invalid media type', 2);
        } 
        
        return $this;
    }
    
    
    /**
     * 
     * @return InternetMediaType[]
     */
    public function getValidInternetMediaTypes() {
        return $this->validInternetMediaTypes;
    }
    
    
    /**
     *
     * @return boolean 
     */
    public function hasValidInternetMediaType() {        
        if (count($this->getValidInternetMediaTypes()) === 0) {
            return true;
        }
        
        return array_key_exists($this->getInternetMediaType()->getTypeSubtypeString(), $this->getValidInternetMediaTypes());
    }    
    

    /**
     * 
     * @param \Guzzle\Http\Message\Response $response
     * @return \webignition\WebResource\WebResource
     */
    public function setHttpResponse(\Guzzle\Http\Message\Response $response) {
        $this->httpResponse = $response;
        
        if (!$this->hasValidInternetMediaType()) {
            throw new Exception('HTTP response contains invalid media type', 2);
        }
        
        return $this;
    }
    
    
    /**
     * 
     * @return \Guzzle\Http\Message\Response
     */
    public function getHttpResponse() {
        if (!$this->hasHttpResponse()) {
            throw new Exception('HTTP response not set', 1);
        }
        
        return $this->httpResponse;
    } 
    
    
    /**
     * 
     * @return boolean
     */
    private function hasHttpResponse() {
        return !is_null($this->httpResponse);
    }
    
    
    /**
     * 
     * @return \webignition\InternetMediaType\InternetMediaType
     */
    public function getInternetMediaType() {
        if (is_null($this->internetMediaType)) {
            $parser = new \webignition\InternetMediaType\Parser\Parser();
            $this->internetMediaType = $parser->parse($this->getHttpResponse()->getContentType());           
        }
        
        return $this->internetMediaType;
    }
}