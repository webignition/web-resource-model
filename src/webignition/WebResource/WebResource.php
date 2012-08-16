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
     * Set content type
     *
     * @param string $contentTypeString
     * @return WebResource
     */
    public function setContentType($contentTypeString)
    {
        $parser = new InternetMediaTypeParser();
        $this->contentType = $parser->parse($contentTypeString);
        return $this;
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
}