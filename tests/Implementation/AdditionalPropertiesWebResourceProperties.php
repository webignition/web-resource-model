<?php

namespace webignition\Tests\WebResource\Implementation;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;
use webignition\WebResource\WebResourceProperties;
use webignition\WebResource\WebResourcePropertiesInterface;

class AdditionalPropertiesWebResourceProperties extends WebResourceProperties
{
    const ARG_TYPE = 'type';

    /**
     * @var string
     */
    private $type;

    public function __construct(
        ?UriInterface $uri = null,
        ?InternetMediaTypeInterface $contentType = null,
        ?string $content = null,
        ?ResponseInterface $response = null,
        ?string $type = null
    ) {
        parent::__construct($uri, $contentType, $content, $response);

        $this->type = $type;
    }

    public static function create(array $args): WebResourcePropertiesInterface
    {
        $uri = isset($args[self::ARG_URI]) ? $args[self::ARG_URI] : null;
        $contentType = isset($args[self::ARG_CONTENT_TYPE]) ? $args[self::ARG_CONTENT_TYPE] : null;
        $content = isset($args[self::ARG_CONTENT]) ? $args[self::ARG_CONTENT] : null;
        $response = isset($args[self::ARG_RESPONSE]) ? $args[self::ARG_RESPONSE] : null;
        $type = isset($args[self::ARG_TYPE]) ? $args[self::ARG_TYPE] : null;

        return new AdditionalPropertiesWebResourceProperties($uri, $contentType, $content, $response, $type);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function hasType(): bool
    {
        return !empty($this->type);
    }
}
