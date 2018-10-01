<?php

namespace webignition\Tests\WebResource\Implementation;

use webignition\WebResource\SpecificContentTypeWebResource;

class AcceptNoneContentTypeWebResource extends SpecificContentTypeWebResource
{
    protected static function getAllowedContentTypeStrings(): ?array
    {
        return null;
    }

    protected static function getAllowedContentTypePatterns(): ?array
    {
        return null;
    }
}
