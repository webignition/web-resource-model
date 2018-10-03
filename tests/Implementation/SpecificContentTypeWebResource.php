<?php

namespace webignition\Tests\WebResource\Implementation;

use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;
use webignition\WebResource\WebResource;

class SpecificContentTypeWebResource extends WebResource
{
    public static function models(InternetMediaTypeInterface $mediaType): bool
    {
        return in_array($mediaType->getTypeSubtypeString(), self::getModelledContentTypeStrings());
    }

    public static function getModelledContentTypeStrings(): array
    {
        return [
            'foo/bar',
        ];
    }
}
