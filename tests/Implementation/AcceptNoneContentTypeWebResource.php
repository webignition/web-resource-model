<?php

namespace webignition\Tests\WebResource\Implementation;

use webignition\WebResource\SpecificContentTypeWebResource;

class AcceptNoneContentTypeWebResource extends SpecificContentTypeWebResource
{
    /**
     * {@inheritdoc}
     */
    protected static function getAllowedContentTypeStrings()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    protected static function getAllowedContentTypePatterns()
    {
        return null;
    }
}
