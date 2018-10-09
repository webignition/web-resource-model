<?php

namespace webignition\Tests\WebResource\Implementation;

use webignition\WebResource\WebResource;
use webignition\WebResource\WebResourcePropertiesInterface;
use webignition\WebResourceInterfaces\WebResourceInterface;

class AdditionalPropertiesWebResource extends WebResource
{
    /**
     * @var string
     */
    private $type;

    public function __construct(WebResourcePropertiesInterface $properties)
    {
        parent::__construct($properties);

        if ($properties instanceof AdditionalPropertiesWebResourceProperties) {
            $this->type = $properties->getType();
        }
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

    public function getType(): string
    {
        return $this->type;
    }

    public function getPropertiesClassName(): string
    {
        return AdditionalPropertiesWebResourceProperties::class;
    }
}
