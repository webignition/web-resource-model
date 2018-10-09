<?php

namespace webignition\WebResource;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use webignition\InternetMediaType\Parser\ParseException as InternetMediaTypeParseException;
use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;
use webignition\WebResource\Exception\InvalidContentTypeException;
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

    /**
     * @param WebResourcePropertiesInterface $properties
     *
     * @throws InvalidContentTypeException
     */
    public function __construct(?WebResourcePropertiesInterface $properties = null)
    {
        if (!empty($properties)) {
            $uri = $properties->getUri();
            $contentType = $properties->getContentType();
            $content = $properties->getContent();
            $response = $properties->getResponse();

            if (empty($contentType)) {
                $contentType = static::getDefaultContentType();
            }

            if ($response) {
                $content = null;

                try {
                    $contentType = ContentTypeFactory::createFromResponse($response);
                    $this->hasInvalidContentType = false;
                } catch (InternetMediaTypeParseException $e) {
                    $this->hasInvalidContentType = true;
                }
            }

            if (!empty($contentType) && !static::models($contentType)) {
                throw new InvalidContentTypeException($contentType);
            }

            $this->uri = $uri;
            $this->contentType = $contentType;
            $this->content = $content;
            $this->response = $response;
        }
    }

    /**
     * @param string $content
     * @param InternetMediaTypeInterface $contentType
     *
     * @return WebResourceInterface
     *
     * @throws InvalidContentTypeException
     */
    public static function createFromContent(
        string $content,
        ?InternetMediaTypeInterface $contentType = null
    ): WebResourceInterface {
        $className = get_called_class();

        return new $className(WebResourceProperties::create([
            WebResourceProperties::ARG_CONTENT => $content,
            WebResourceProperties::ARG_CONTENT_TYPE => $contentType,
        ]));
    }

    /**
     * @param UriInterface $uri
     * @param ResponseInterface $response
     *
     * @return WebResourceInterface
     *
     * @throws InvalidContentTypeException
     */
    public static function createFromResponse(UriInterface $uri, ResponseInterface $response): WebResourceInterface
    {
        $className = get_called_class();

        return new $className(WebResourceProperties::create([
            WebResourceProperties::ARG_URI => $uri,
            WebResourceProperties::ARG_RESPONSE => $response,
        ]));
    }

    protected function getPropertiesClassName(): string
    {
        return WebResourceProperties::class;
    }

    protected function mergeProperties(?WebResourcePropertiesInterface $properties): WebResourcePropertiesInterface
    {
        $propertiesClassName = $this->getPropertiesClassName();

        $uri = $properties->hasUri() ? $properties->getUri() : $this->uri;
        $contentType = $properties->hasContentType() ? $properties->getContentType() : $this->contentType;
        $content = $properties->hasContent() ? $properties->getContent() : $this->content;
        $response = $properties->hasResponse() ? $properties->getResponse() : $this->response;

        return new $propertiesClassName(
            $uri,
            $contentType,
            $content,
            $response
        );
    }

    /**
     * Allow a default content type to defined.
     *
     * Classes extending WebResource and which are scoped to a specific content type may want to override
     * this method to remove the need for the content type to be passed when creating a new instance.
     *
     * @return InternetMediaTypeInterface|null
     */
    public static function getDefaultContentType(): ?InternetMediaTypeInterface
    {
        return null;
    }

    public function setUri(UriInterface $uri): WebResourceInterface
    {
        return $this->createNewInstance([
            WebResourceProperties::ARG_URI => $uri,
        ]);
    }

    public function getUri(): ?UriInterface
    {
        return $this->uri;
    }

    public function setContentType(InternetMediaTypeInterface $contentType): WebResourceInterface
    {
        $response = null;

        if (!empty($this->response)) {
            $response = $this->response->withHeader(self::HEADER_CONTENT_TYPE, (string)$contentType);
        }

        return $this->createNewInstance([
            WebResourceProperties::ARG_CONTENT_TYPE => $contentType,
            WebResourceProperties::ARG_RESPONSE => $response,
        ]);
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

        return $this->createNewInstance([
            WebResourceProperties::ARG_CONTENT_TYPE => $contentType,
            WebResourceProperties::ARG_CONTENT => $content,
            WebResourceProperties::ARG_RESPONSE => $response,
        ]);
    }

    public function getContent(): ?string
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
        return $this->createNewInstance([
            WebResourceProperties::ARG_CONTENT => null,
            WebResourceProperties::ARG_RESPONSE => $response,
        ]);
    }

    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    protected function createNewInstance(array $args): WebResourceInterface
    {
        /* @var WebResourceProperties $propertiesClassName */
        $propertiesClassName = $this->getPropertiesClassName();
        $className = get_called_class();

        return new $className($this->mergeProperties($propertiesClassName::create($args)));
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
