<?php

namespace webignition\WebResource;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;

class WebResourceProperties implements WebResourcePropertiesInterface
{
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

    public function __construct(
        ?UriInterface $uri = null,
        ?InternetMediaTypeInterface $contentType = null,
        ?string $content = null,
        ?ResponseInterface $response = null
    ) {
        $this->uri = $uri;
        $this->contentType = $contentType;
        $this->content = $content;
        $this->response = $response;
    }

    public static function create(array $args): WebResourcePropertiesInterface
    {
        $uri = isset($args[self::ARG_URI]) ? $args[self::ARG_URI] : null;
        $contentType = isset($args[self::ARG_CONTENT_TYPE]) ? $args[self::ARG_CONTENT_TYPE] : null;
        $content = isset($args[self::ARG_CONTENT]) ? $args[self::ARG_CONTENT] : null;
        $response = isset($args[self::ARG_RESPONSE]) ? $args[self::ARG_RESPONSE] : null;

        return new WebResourceProperties($uri, $contentType, $content, $response);
    }

    public function getUri(): ?UriInterface
    {
        return $this->uri;
    }

    public function hasUri(): bool
    {
        return !empty($this->uri);
    }

    public function getContentType(): ?InternetMediaTypeInterface
    {
        return $this->contentType;
    }

    public function hasContentType(): bool
    {
        return !empty($this->contentType);
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function hasContent(): bool
    {
        return !empty($this->content);
    }


    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    public function hasResponse(): bool
    {
        return !empty($this->response);
    }
}
