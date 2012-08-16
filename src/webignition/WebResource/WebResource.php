<?php
namespace webignition\WebResource;

use webignition\InternetMediaType\Parser\Parser as InternetMediaTypeParser;
use webignition\InternetMediaType\InternetMediaType;

/**
 * Models a web-based resource
 */
class WebResource
{    
    
    /**
     * Full absolute URL from which the resource was retrieved
     * 
     * @var string
     * 
     */
    private $url;
    
    
    /**
     *
     * @var InternetMediaType
     */
    private $contentType;
    
    
    /**
     *
     * @var string
     */
    private $content;
    
    
    /**
     *  Collection of valid internet media type types and subtypes allowed for
     *  this resource
     * 
     * @var array
     */
    private $validContentTypes = array();
    
    
    /**
     *
     * @param InternetMediaType $contentType
     * @return \webignition\WebResource\WebResource 
     */
    public function addValidContentType(InternetMediaType $contentType) {
        if (!$this->hasValidContentType($contentType)) {
            $this->validContentTypes[$contentType->getType() . '/' . $contentType->getSubtype()] = $contentType;
        }
        
        return $this;        
    }
    
    
    /**
     *
     * @param InternetMediaType $contentType
     * @return \webignition\WebResource\WebResource 
     */
    public function removeValidContentType(InternetMediaType $contentType) {
        if ($this->hasValidContentType($contentType)) {
            unset($this->validContentTypes[$contentType->getType() . '/' . $contentType->getSubtype()]);
        }
        
        return $this;
    }
    
    
    /**
     *
     * @param InternetMediaType $contentType
     * @return boolean 
     */
    private function hasValidContentType(InternetMediaType $contentType) {
        return array_key_exists($contentType->getType() . '/' . $contentType->getSubtype(), $this->validContentTypes );
    }
    
    

    /**
     * Set url
     *
     * @param string $url
     * @return WebResource
     */
    public function setUrl($url)
    {
        $this->url = $url;    
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get content type
     *
     * @return string 
     */
    public function getContentType()
    {
        return $this->contentType;
    }    
    
    
    /**
     * Set content
     *
     * @param string $content
     * @return WebResource
     */
    public function setContent($content)
    {
        $this->content = $content;    
        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }
    
    /**
     * Set content type
     *
     * @param string $contentTypeString
     * @return WebResource
     */
    public function setContentType($contentTypeString) {
        $mediaTypeParser = new InternetMediaTypeParser();
        $contentType = $mediaTypeParser->parse($contentTypeString);

        if (!$this->isValidContentType($contentType)) {
            throw new Exception('Invalid content type: "'.$contentTypeString.'"', 1);
        }
       
        $this->contentType = $contentType;
        return $this;
    }
    
    
    /**
     *
     * @param InternetMediaType $contentType
     * @return boolean 
     */
    private function isValidContentType(InternetMediaType $contentType) {
        if (count($this->validContentTypes) === 0) {
            return true;
        }
        
        return $this->hasValidContentType($contentType);
    }    
}