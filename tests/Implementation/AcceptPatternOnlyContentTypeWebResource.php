<?php

namespace webignition\Tests\WebResource\Implementation;

use webignition\WebResource\SpecificContentTypeWebResource;

class AcceptPatternOnlyContentTypeWebResource extends SpecificContentTypeWebResource
{
    protected static function getAllowedContentTypeStrings(): ?array
    {
        return null;
    }

    protected static function getAllowedContentTypePatterns(): array
    {
        return [
            '/application\/./'
        ];
    }
}
