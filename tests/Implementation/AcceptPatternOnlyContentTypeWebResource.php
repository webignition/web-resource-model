<?php

namespace webignition\Tests\WebResource\Implementation;

use webignition\WebResource\SpecificContentTypeWebResource;

class AcceptPatternOnlyContentTypeWebResource extends SpecificContentTypeWebResource
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
        return [
            '/application\/./'
        ];
    }
}
