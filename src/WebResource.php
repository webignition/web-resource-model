<?php

namespace webignition\WebResource;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use webignition\InternetMediaType\Parser\ParseException as InternetMediaTypeParseException;
use webignition\InternetMediaType\Parser\Parser as InternetMediaTypeParser;
use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;
use webignition\WebResourceInterfaces\WebResourceInterface;

/**
 * Models a web-based resource by providing access to commonly-used aspects
 * of a PSR-7 HTTP response
 */
class WebResource implements WebResourceInterface
{
    const HEADER_CONTENT_TYPE = 'content-type';

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var InternetMediaTypeInterface
     */
    private $contentType;

    /**
     * @var UriInterface
     */
    private $uri;

    /**
     * @param ResponseInterface $response
     * @param UriInterface|null $uri
     *
     * @throws InternetMediaTypeParseException
     */
    public function __construct(ResponseInterface $response, UriInterface $uri = null)
    {
        $this->response = $response;
        $this->uri = $uri;
        $this->contentType = $this->createContentTypeFromResponse($response);
    }

    public function setResponse(ResponseInterface $response): WebResourceInterface
    {
        $className = get_class($this);

        return new $className($response, $this->getUri());
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function setUri(UriInterface $uri): WebResourceInterface
    {
        $className = get_class($this);

        return new $className($this->getResponse(), $uri);
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function getContentType(): InternetMediaTypeInterface
    {
        return $this->contentType;
    }

    public function setBody(StreamInterface $body): WebResourceInterface
    {
        $newResponse = $this->response->withBody($body);
        $className = get_class($this);

        return new $className($newResponse, $this->getUri());
    }

    public function getBody(): ?StreamInterface
    {
        return $this->response->getBody();
    }

    public function getContent(): string
    {
        $resourceContent = (string)$this->response->getBody();
        $content = @gzdecode($resourceContent);

        if (false === $content) {
            $content = $resourceContent;
        }

        return $content;
    }

    /**
     * A generic WebResource can be used to model anything.
     *
     * Classes extending WebResource should limit the types of content that they can model
     * by overriding this method directly, or by overriding the getAllowedContentTypeStrings() or
     * getAllowedContentTypePatterns() methods.
     *
     * {@inheritdoc}
     */
    public static function models(InternetMediaTypeInterface $mediaType): bool
    {
        return true;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return InternetMediaTypeInterface
     *
     * @throws InternetMediaTypeParseException
     */
    private function createContentTypeFromResponse(ResponseInterface $response): InternetMediaTypeInterface
    {
        $internetMediaTypeParser = new InternetMediaTypeParser();
        $internetMediaTypeParserConfiguration = $internetMediaTypeParser->getConfiguration();
        $internetMediaTypeParserConfiguration->enableIgnoreInvalidAttributes();
        $internetMediaTypeParserConfiguration->enableAttemptToRecoverFromInvalidInternalCharacter();

        $contentTypeHeader = $response->getHeader(self::HEADER_CONTENT_TYPE);
        $contentTypeString = empty($contentTypeHeader)
            ? ''
            : $contentTypeHeader[0];

        return $internetMediaTypeParser->parse($contentTypeString);
    }

    /**
     * {@inheritdoc}
     */
    public static function getModelledContentTypeStrings(): array
    {
        return [];
    }
}
