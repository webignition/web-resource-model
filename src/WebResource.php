<?php
namespace webignition\WebResource;

use \webignition\InternetMediaType\InternetMediaType;
use \webignition\InternetMediaType\Parser\Parser as InternetMediaTypeParser;
use \GuzzleHttp\Message\ResponseInterface as HttpResponseInterface;
use \GuzzleHttp\Stream\Stream as GuzzleStream;

/**
 * Models a web-based resource by providing access to commonly-used aspects
 * of a HTTP response
 */
class WebResource
{
    /**
     * @var HttpResponseInterface
     */
    private $httpResponse;

    /**
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
     * @var string
     */
    private $url;

    /**
     * @var \webignition\InternetMediaType\Parser\Parser
     */
    private $internetMediaTypeParser;

    /**
     * @param InternetMediaType $contentType
     * @throws Exception
     *
     * @return self
     */
    public function addValidContentType(InternetMediaType $contentType)
    {
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
     * @param InternetMediaType $contentType
     * @throws Exception
     *
     * @return self
     */
    public function removeValidContentType(InternetMediaType $contentType)
    {
        if (array_key_exists($contentType->getTypeSubtypeString(), $this->validContentTypes)) {
            unset($this->validContentTypes[$contentType->getTypeSubtypeString()]);
        }

        if ($this->hasHttpResponse() && !$this->hasValidContentType()) {
            throw new Exception('HTTP response contains invalid content type', 2);
        }

        return $this;
    }

    /**
     * @return InternetMediaType[]
     */
    public function getValidContentTypes()
    {
        return $this->validContentTypes;
    }

    /**
     *
     * @return boolean
     */
    public function hasValidContentType()
    {
        if (empty($this->getValidContentTypes())) {
            return true;
        }

        return array_key_exists($this->getContentType()->getTypeSubtypeString(), $this->getValidContentTypes());
    }

    /**
     * @param HttpResponseInterface $response
     * @throws Exception
     *
     * @return self
     */
    public function setHttpResponse(HttpResponseInterface $response)
    {
        $this->httpResponse = $response;

        if (!$this->hasValidContentType()) {
            throw new Exception('HTTP response contains invalid content type', 2);
        }

        $this->contentType = null;

        return $this;
    }

    /**
     * @return HttpResponseInterface
     * @throws Exception
     */
    public function getHttpResponse()
    {
        if (!$this->hasHttpResponse()) {
            throw new Exception('HTTP response not set', 1);
        }

        return $this->httpResponse;
    }

    /**
     * @return boolean
     */
    public function hasHttpResponse()
    {
        return !is_null($this->httpResponse);
    }

    /**
     * @return InternetMediaType
     */
    public function getContentType()
    {
        if (is_null($this->contentType)) {
            $this->contentType = $this->getInternetMediaTypeParser()->parse(
                $this->getHttpResponse()->getHeader('content-type')
            );
        }

        return $this->contentType;
    }

    /**
     *
     * @return InternetMediaTypeParser
     */
    public function getInternetMediaTypeParser()
    {
        if (is_null($this->internetMediaTypeParser)) {
            $this->internetMediaTypeParser = new InternetMediaTypeParser();
            $this->internetMediaTypeParser->getConfiguration()->enableIgnoreInvalidAttributes();
            $this->internetMediaTypeParser->getConfiguration()->enableAttemptToRecoverFromInvalidInternalCharacter();
        }

        return $this->internetMediaTypeParser;
    }

    /**
     * @return string|null
     */
    public function getContent()
    {
        return $this->hasHttpResponse() ? $this->getResponseContent() : null;
    }

    /**
     * Used to catch errors that will occur when running gzdecode
     * on plain text.
     *
     * Can't tell if response body content is gzip encoded; headers and body
     * may not match.
     *
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @throws ContentDecodeException
     * @throws \ErrorException
     */
    private function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if (substr_count($errstr, 'gzdecode')) {
            throw new ContentDecodeException('Unable to gzdecode content', 1);
        }

        /**
         * gzip return code 2 indicates a warning as documented at
         * http://unixhelp.ed.ac.uk/CGI/man-cgi?gzip
         *
         * In this case, the warning is /very likely/ related to trying to
         * decompress plain text
         *
         * PHP 5.3+ gzdecode returns the plain string given to it as-is
         * HHVM 3.5+ gzdecode raises a warning when given a plain string
         */
        if ($errno === 2) {
            throw new ContentDecodeException('Unable to gzdecode probably-plain content', 2);
        }

        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    /**
     * @return string
     */
    private function getResponseContent()
    {
        set_error_handler(array(&$this, 'errorHandler'));

        $resourceContent = (string)$this->getHttpResponse()->getBody();

        try {
            $content = gzdecode($resourceContent);
            restore_error_handler();
            return $content;
        } catch (ContentDecodeException $contentDecodeException) {
            restore_error_handler();
            return $resourceContent;
        }
    }

    /**
     * @param string $content
     *
     * @return self
     */
    public function setContent($content)
    {
        $this->getHttpResponse()->setBody(GuzzleStream::factory($content));

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrl()
    {
        if (!$this->hasHttpResponse()) {
            return $this->url;
        }

        return $this->getHttpResponse()->getEffectiveUrl();
    }

    /**
     * @param string $url
     *
     * @return self
     */
    public function setUrl($url)
    {
        if ($this->hasHttpResponse()) {
            $this->getHttpResponse()->setEffectiveUrl($url);
        } else {
            $this->url = $url;
        }

        return $this;
    }
}
