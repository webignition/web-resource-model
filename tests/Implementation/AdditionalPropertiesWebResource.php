<?php

namespace webignition\Tests\WebResource\Implementation;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;
use webignition\WebResource\Exception\InvalidContentTypeException;
use webignition\WebResource\WebResource;
use webignition\WebResource\WebResourceProperties;
use webignition\WebResource\WebResourcePropertiesInterface;
use webignition\WebResourceInterfaces\WebResourceInterface;

class AdditionalPropertiesWebResource extends WebResource
{
    /**
     * @var string
     */
    private $type;

    public function __construct(?WebResourcePropertiesInterface $properties = null)
    {
        parent::__construct($properties);

        if ($properties instanceof AdditionalPropertiesWebResourceProperties) {
            $this->type = $properties->getType();
        }
    }

    /**
     * @param string $content
     * @param InternetMediaTypeInterface $contentType
     * @param string|null $type
     *
     * @return AdditionalPropertiesWebResource
     *
     * @throws InvalidContentTypeException
     */
    public static function createFromContent(
        string $content,
        ?InternetMediaTypeInterface $contentType = null,
        ?string $type = null
    ): WebResourceInterface {
        $className = get_called_class();

        return new $className(AdditionalPropertiesWebResourceProperties::create([
            AdditionalPropertiesWebResourceProperties::ARG_CONTENT => $content,
            AdditionalPropertiesWebResourceProperties::ARG_CONTENT_TYPE => $contentType,
            AdditionalPropertiesWebResourceProperties::ARG_TYPE => $type,
        ]));
    }

    /**
     * @param UriInterface $uri
     * @param ResponseInterface $response
     * @param null|string $type
     *
     * @return AdditionalPropertiesWebResource
     *
     * @throws InvalidContentTypeException
     */
    public static function createFromResponse(
        UriInterface $uri,
        ResponseInterface $response,
        ?string $type = null
    ): WebResourceInterface {
        $className = get_called_class();

        return new $className(AdditionalPropertiesWebResourceProperties::create([
            AdditionalPropertiesWebResourceProperties::ARG_URI => $uri,
            AdditionalPropertiesWebResourceProperties::ARG_RESPONSE => $response,
            AdditionalPropertiesWebResourceProperties::ARG_TYPE => $type,
        ]));
    }

    protected function mergeProperties(?WebResourcePropertiesInterface $properties): WebResourcePropertiesInterface
    {
        $type = $this->type;

        if ($properties instanceof AdditionalPropertiesWebResourceProperties) {
            $type = $properties->hasType() ? $properties->getType() : $type;
        }

        $parentProperties = parent::mergeProperties($properties);

        return new AdditionalPropertiesWebResourceProperties(
            $parentProperties->getUri(),
            $parentProperties->getContentType(),
            $parentProperties->getContent(),
            $parentProperties->getResponse(),
            $type
        );
    }

    public function setType(string $type): AdditionalPropertiesWebResource
    {
        /* @var AdditionalPropertiesWebResource|WebResourceInterface $newInstance */
        $newInstance = $this->createNewInstance([
            AdditionalPropertiesWebResourceProperties::ARG_TYPE => $type,
        ]);

        return $newInstance;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getPropertiesClassName(): string
    {
        return AdditionalPropertiesWebResourceProperties::class;
    }
}
