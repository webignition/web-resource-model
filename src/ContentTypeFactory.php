<?php

namespace webignition\WebResource;

use Psr\Http\Message\ResponseInterface;
use webignition\InternetMediaType\Parser\Parser as InternetMediaTypeParser;
use webignition\InternetMediaType\Parser\ParseException as InternetMediaTypeParseException;
use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;

class ContentTypeFactory
{
    const HEADER_CONTENT_TYPE = 'content-type';

    /**
     * @param ResponseInterface $response
     *
     * @return null|InternetMediaTypeInterface
     *
     * @throws InternetMediaTypeParseException
     */
    public static function createFromResponse(ResponseInterface $response): ?InternetMediaTypeInterface
    {
        $contentType = null;

        $internetMediaTypeParser = new InternetMediaTypeParser();
        $internetMediaTypeParserConfiguration = $internetMediaTypeParser->getConfiguration();
        $internetMediaTypeParserConfiguration->enableIgnoreInvalidAttributes();
        $internetMediaTypeParserConfiguration->enableAttemptToRecoverFromInvalidInternalCharacter();

        return $internetMediaTypeParser->parse($response->getHeaderLine(self::HEADER_CONTENT_TYPE));
    }
}
