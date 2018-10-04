<?php

namespace webignition\Tests\WebResource\Implementation;

use webignition\InternetMediaType\InternetMediaType;
use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;
use webignition\WebResource\WebResource;

class SpecificContentTypeWebResourceWithDefaultContentType extends WebResource
{
    const CONTENT_TYPE_TYPE = 'foo';
    const CONTENT_TYPE_SUBTYPE = 'bar';

    public static function models(InternetMediaTypeInterface $mediaType): bool
    {
        return in_array($mediaType->getTypeSubtypeString(), self::getModelledContentTypeStrings());
    }

    public static function getModelledContentTypeStrings(): array
    {
        return [
            self::CONTENT_TYPE_TYPE . '/' . self::CONTENT_TYPE_SUBTYPE,
        ];
    }

    public static function getDefaultContentType(): InternetMediaType
    {
        $contentType = new InternetMediaType();
        $contentType->setType(self::CONTENT_TYPE_TYPE);
        $contentType->setSubtype(self::CONTENT_TYPE_SUBTYPE);

        return $contentType;
    }
}
