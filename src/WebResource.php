<?php

namespace webignition\WebResource;

use Psr\Http\Message\ResponseInterface;
use \webignition\InternetMediaType\InternetMediaType;
use \webignition\InternetMediaType\Parser\Parser as InternetMediaTypeParser;

/**
 * Models a web-based resource by providing access to commonly-used aspects
 * of a PSR-7 HTTP response
 */
class WebResource
{
    const HEADER_CONTENT_TYPE = 'content-type';

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var InternetMediaType
     */
    private $contentType;

    /**
     * @var string
     */
    private $url;

    /**
     * @param ResponseInterface $response
     * @param string $url
     */
    public function __construct(ResponseInterface $response, $url)
    {
        $this->response = $response;
        $this->url = $url;

        $internetMediaTypeParser = new InternetMediaTypeParser();
        $internetMediaTypeParserConfiguration = $internetMediaTypeParser->getConfiguration();
        $internetMediaTypeParserConfiguration->enableIgnoreInvalidAttributes();
        $internetMediaTypeParserConfiguration->enableAttemptToRecoverFromInvalidInternalCharacter();

        $contentTypeHeader = $response->getHeader(self::HEADER_CONTENT_TYPE);
        $contentTypeString = empty($contentTypeHeader)
            ? ''
            : $contentTypeHeader[0];

        $this->contentType = $internetMediaTypeParser->parse($contentTypeString);
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return InternetMediaType
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @return string|null
     */
    public function getContent()
    {
        $resourceContent = (string)$this->response->getBody();
        $content = @gzdecode($resourceContent);

        if (false === $content) {
            $content = $resourceContent;
        }

        return $content;
    }
}
