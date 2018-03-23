<?php

namespace webignition\Tests\WebResource\Implementation;

use webignition\WebResource\SpecificContentTypeWebResource;

class AcceptStringOrPatternContentTypeWebResource extends SpecificContentTypeWebResource
{
    /**
     * {@inheritdoc}
     */
    protected static function getAllowedContentTypeStrings()
    {
        return [
            'text/plain',
            'text/html',
        ];
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
