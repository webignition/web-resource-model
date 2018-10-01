<?php

namespace webignition\Tests\WebResource\Implementation;

use webignition\WebResource\SpecificContentTypeWebResource;

class AcceptStringOnlyContentTypeWebResource extends SpecificContentTypeWebResource
{
    protected static function getAllowedContentTypeStrings(): array
    {
        return [
            'text/plain',
            'text/html',
        ];
    }

    protected static function getAllowedContentTypePatterns(): ?array
    {
        return null;
    }
}
