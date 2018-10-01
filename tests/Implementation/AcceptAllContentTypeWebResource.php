<?php

namespace webignition\Tests\WebResource\Implementation;

use webignition\WebResource\SpecificContentTypeWebResource;

class AcceptAllContentTypeWebResource extends SpecificContentTypeWebResource
{
    protected static function getAllowedContentTypeStrings(): array
    {
        return [];
    }

    protected static function getAllowedContentTypePatterns(): array
    {
        return [];
    }
}
