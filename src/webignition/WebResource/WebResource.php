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
    private $contentType;
    
    
    /**
     *  Collection of valid internet media type types and subtypes allowed for
     *  this resource
     * 
     * @var InternetMediaType[]
     */
    private $validContentTypes = array();

    
    /**
     *
     * @param InternetMediaType $contentType
     * @return \webignition\WebResource\WebResource 
     */
    public function addValidContentType(InternetMediaType $contentType) {
        $addedMediaType = new InternetMediaType();
        $addedMediaType->setType($contentType->getType());
        $addedMediaType->setSubtype($contentType->getSubtype());
        
        $this->validContentTypes[$addedMediaType->getTypeSubtypeString()] = $addedMediaType;
        
        if ($this->hasHttpResponse() && !$this->hasValidContentType()) {
            throw new Exception('HTTP response contains invalid content type', 2);
        }        
        
        return $this;        
    }
    
    
    /**
     *
     * @param InternetMediaType $contentType
     * @return \webignition\WebResource\WebResource 
     */
    public function removeValidContentType(InternetMediaType $contentType) {
        if (array_key_exists($contentType->getTypeSubtypeString(), $this->validContentTypes)) {
            unset($this->validContentTypes[$contentType->getTypeSubtypeString()]);
        }
        
        if ($this->hasHttpResponse() && !$this->hasValidContentType()) {
            throw new Exception('HTTP response contains invalid content type', 2);
        } 
        
        return $this;
    }
    
    
    /**
     * 
     * @return InternetMediaType[]
     */
    public function getValidContentTypes() {
        return $this->validContentTypes;
    }
    
    
    /**
     *
     * @return boolean 
     */
    public function hasValidContentType() {        
        if (count($this->getValidContentTypes()) === 0) {
            return true;
        }
        
        return array_key_exists($this->getContentType()->getTypeSubtypeString(), $this->getValidContentTypes());
    }    
    

    /**
     * 
     * @param \Guzzle\Http\Message\Response $response
     * @return \webignition\WebResource\WebResource
     */
    public function setHttpResponse(\Guzzle\Http\Message\Response $response) {
        $this->httpResponse = $response;
        
        if (!$this->hasValidContentType()) {
            throw new Exception('HTTP response contains invalid content type', 2);
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
    public function getContentType() {
        if (is_null($this->contentType)) {
            $parser = new \webignition\InternetMediaType\Parser\Parser();
            $this->contentType = $parser->parse($this->getHttpResponse()->getContentType());           
        }
        
        return $this->contentType;
    }
    
    
    /**
     * 
     * @return string
     */
    public function getContent() {
        return $this->getHttpResponse()->getBody(true);
    }
    
    
    /**
     * 
     * @param string $content
     * @return \webignition\WebResource\WebResource
     */
    public function setContent($content) {
        $this->getHttpResponse()->setBody($content);
        return $this;
    }
    
    
    /**
     * 
     * @return string
     */
    public function getUrl() {
        return $this->getHttpResponse()->getEffectiveUrl();
    }
}