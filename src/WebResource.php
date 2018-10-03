<?php

namespace webignition\WebResource;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use webignition\InternetMediaType\Parser\ParseException as InternetMediaTypeParseException;
use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;
use webignition\WebResource\Exception\ReadOnlyResponseException;
use webignition\WebResource\Exception\UnseekableResponseException;
use webignition\WebResourceInterfaces\WebResourceInterface;

class WebResource implements WebResourceInterface
{
    const HEADER_CONTENT_TYPE = 'content-type';

    const ARG_URI = 'uri';
    const ARG_CONTENT_TYPE = 'content-type';
    const ARG_CONTENT = 'content';
    const ARG_RESPONSE = 'response';

    /**
     * @var UriInterface
     */
    private $uri;

    /**
     * @var InternetMediaTypeInterface
     */
    private $contentType;

    /**
     * @var string|null
     */
    private $content;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var bool
     */
    private $hasInvalidContentType = null;

    private function __construct(array $args)
    {
        $this->uri = $args[self::ARG_URI];

        if (isset($args[self::ARG_RESPONSE])) {
            $this->response = $args[self::ARG_RESPONSE];

            $contentType = null;

            try {
                $contentType = ContentTypeFactory::createFromResponse($this->response);
                $this->hasInvalidContentType = false;
            } catch (InternetMediaTypeParseException $e) {
                $this->hasInvalidContentType = true;
            }

            $args[self::ARG_CONTENT_TYPE] = $contentType;
            $args[self::ARG_CONTENT] = null;
        }

        $this->contentType = $args[self::ARG_CONTENT_TYPE];
        $this->content = $args[self::ARG_CONTENT];
    }

    public static function createFromContent(
        UriInterface $uri,
        string $content,
        ?InternetMediaTypeInterface $contentType
    ): WebResourceInterface {
        return new self([
            self::ARG_URI => $uri,
            self::ARG_CONTENT_TYPE => $contentType,
            self::ARG_CONTENT => $content,
        ]);
    }

    public static function createFromResponse(UriInterface $uri, ResponseInterface $response): WebResourceInterface
    {
        return new self([
            self::ARG_URI => $uri,
            self::ARG_RESPONSE => $response,
        ]);
    }

    public function setUri(UriInterface $uri): WebResourceInterface
    {
        return $this->createNewInstance($uri, $this->contentType, $this->content, $this->response);
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function setContentType(InternetMediaTypeInterface $contentType): WebResourceInterface
    {
        $response = null;

        if (!empty($this->response)) {
            $response = $this->response->withHeader(self::HEADER_CONTENT_TYPE, (string)$contentType);
        }

        return $this->createNewInstance($this->uri, $contentType, $this->content, $response);
    }

    public function getContentType(): ?InternetMediaTypeInterface
    {
        return $this->contentType;
    }

    public function hasInvalidContentType(): bool
    {
        return $this->hasInvalidContentType;
    }

    /**
     * @param string $content
     *
     * @return WebResourceInterface
     *
     * @throws ReadOnlyResponseException
     * @throws UnseekableResponseException
     */
    public function setContent(string $content): WebResourceInterface
    {
        if (empty($this->response)) {
            $response = null;
            $contentType = $this->contentType;
        } else {
            $response = $this->setResponseBodyContent($content);
            $content = null;
            $contentType = null;
        }

        return $this->createNewInstance($this->uri, $contentType, $content, $response);
    }

    public function getContent(): string
    {
        if (empty($this->response)) {
            return $this->content;
        }

        $resourceContent = (string)$this->response->getBody();
        $content = @gzdecode($resourceContent);

        if (false === $content) {
            $content = $resourceContent;
        }

        return $content;
    }

    public function setResponse(ResponseInterface $response): WebResourceInterface
    {
        $contentType = null;
        $content = null;

        return $this->createNewInstance($this->uri, $contentType, $content, $response);
    }

    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    private function createNewInstance(
        UriInterface $uri,
        ?InternetMediaTypeInterface $contentType,
        ?string $content,
        ?ResponseInterface $response
    ): WebResourceInterface {
        $args = [
            self::ARG_URI => $uri,
        ];

        if (empty($response)) {
            $args[self::ARG_CONTENT] = $content;
            $args[self::ARG_CONTENT_TYPE] = $contentType;
        } else {
            $args[self::ARG_RESPONSE] = $response;
        }

        return new self($args);
    }

    /**
     * @param string $content
     *
     * @return ResponseInterface
     *
     * @throws ReadOnlyResponseException
     * @throws UnseekableResponseException
     */
    private function setResponseBodyContent(string $content): ResponseInterface
    {
        $responseBody = $this->response->getBody();

        if (!$responseBody->isWritable()) {
            throw new ReadOnlyResponseException();
        }

        if (!$responseBody->isSeekable()) {
            throw new UnseekableResponseException();
        }

        $updatedResponseBody = clone $responseBody;

        $updatedResponseBody->rewind();
        $updatedResponseBody->write($content);
        $updatedResponseBody->rewind();

        return $this->response->withBody($updatedResponseBody);
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
     * {@inheritdoc}
     */
    public static function getModelledContentTypeStrings(): array
    {
        return [];
    }
}
