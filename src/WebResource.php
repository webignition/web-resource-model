<?php

namespace webignition\WebResource;

use Psr\Http\Message\UriInterface;
use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;
use webignition\WebResourceInterfaces\WebResourceInterface;

class WebResource implements WebResourceInterface
{
    /**
     * @var UriInterface
     */
    protected $uri;

    /**
     * @var InternetMediaTypeInterface
     */
    protected $contentType;

    /**
     * @var string
     */
    private $content;

    public function __construct(UriInterface $uri, InternetMediaTypeInterface $contentType, string $content)
    {
        $this->uri = $uri;
        $this->contentType = $contentType;
        $this->content = $content;
    }

    public function setUri(UriInterface $uri): WebResourceInterface
    {
        return $this->createNewInstance($uri, $this->contentType, $this->content);
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function setContentType(InternetMediaTypeInterface $contentType): WebResourceInterface
    {
        return $this->createNewInstance($this->uri, $contentType, $this->content);
    }

    public function getContentType(): InternetMediaTypeInterface
    {
        return $this->contentType;
    }

    public function setContent(string $content): WebResourceInterface
    {
        return $this->createNewInstance($this->uri, $this->contentType, $content);
    }

    public function getContent(): string
    {
        return $this->content;
    }

    private function createNewInstance(
        UriInterface $uri,
        InternetMediaTypeInterface $contentType,
        string $content
    ): WebResourceInterface {
        $className = get_class($this);

        return new $className($uri, $contentType, $content);
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
