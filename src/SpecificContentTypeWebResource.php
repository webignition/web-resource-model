<?php

namespace webignition\WebResource;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use webignition\InternetMediaType\Parser\ParseException as InternetMediaTypeParseException;
use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;
use webignition\WebResource\Exception\InvalidContentTypeException;

/**
 * Models a web resource that is relevant only a set of specific content types
 *
 * Extend and override models() directly, or by override the getAllowedContentTypeStrings() and
 * getAllowedContentTypePatterns() methods.
 */
class SpecificContentTypeWebResource extends WebResource
{
    /**
     * @param ResponseInterface $response
     * @param UriInterface|null $uri
     *
     * @throws InternetMediaTypeParseException
     * @throws InvalidContentTypeException
     */
    public function __construct(ResponseInterface $response, UriInterface $uri = null)
    {
        parent::__construct($response, $uri);

        $contentType = $this->getContentType();

        if (!self::models($contentType)) {
            throw new InvalidContentTypeException($contentType);
        }
    }

    /**
     * A generic WebResource can be used to model anything.
     *
     * Classes extending WebResource should limit the types of content that they can model
     * by overriding this method directly, or by overriding the getAllowedContentTypeStrings() and
     * getAllowedContentTypePatterns() methods.
     *
     * getAllowedContentTypeStrings()  |  getAllowedContentTypePatterns  | meaning
     * ------------------------------------------------------------------------------------------
     * null                            |  null                           |  unspecified, don't do this
     * empty array                     |  null                           |  always true
     * non-empty array                 |  null                           |  check string
     * null                            |  empty array                    |  always true
     * empty array                     |  empty array                    |  always true
     * non-empty array                 |  empty array                    |  always true
     * null                            |  non-empty array                |  check pattern
     * empty array                     |  non-empty array                |  always true
     * non-empty array                 |  non-empty array                |  check string, check pattern
     *
     * {@inheritdoc}
     */
    public static function models(InternetMediaTypeInterface $mediaType)
    {
        $contentTypeSubtypeString = $mediaType->getTypeSubtypeString();

        $allowedContentTypeStrings = static::getAllowedContentTypeStrings();
        $allowedContentTypePatterns = static::getAllowedContentTypePatterns();

        $allowAllByStringMatch = is_array($allowedContentTypeStrings) && empty($allowedContentTypeStrings);
        $allowAllByPatternMatch = is_array($allowedContentTypePatterns) && empty($allowedContentTypePatterns);

        if ($allowAllByStringMatch || $allowAllByPatternMatch) {
            return true;
        }

        $hasValidContentType = false;

        $expectContentTypeString = is_array($allowedContentTypeStrings) && !empty($allowedContentTypeStrings);
        $expectContentTypePattern =
            is_array($allowedContentTypePatterns) && !empty($allowedContentTypePatterns);

        if ($expectContentTypeString && null === $allowedContentTypePatterns) {
            // Content type string match only
            foreach ($allowedContentTypeStrings as $allowedContentType) {
                if (!$hasValidContentType) {
                    $hasValidContentType = $contentTypeSubtypeString === $allowedContentType;
                }
            }

            return $hasValidContentType;
        }

        if ($expectContentTypePattern && null === $allowedContentTypeStrings) {
            // Content type pattern match only
            foreach ($allowedContentTypePatterns as $allowedContentTypePattern) {
                if (!$hasValidContentType) {
                    $hasValidContentType = preg_match($allowedContentTypePattern, $contentTypeSubtypeString) > 0;
                }
            }

            return $hasValidContentType;
        }

        if ($expectContentTypeString && $expectContentTypePattern) {
            // Content type string match, fallback to content type pattern match
            foreach ($allowedContentTypeStrings as $allowedContentType) {
                if (!$hasValidContentType) {
                    $hasValidContentType = $contentTypeSubtypeString === $allowedContentType;
                }
            }

            if (!$hasValidContentType) {
                foreach ($allowedContentTypePatterns as $allowedContentTypePattern) {
                    if (!$hasValidContentType) {
                        $hasValidContentType = preg_match($allowedContentTypePattern, $contentTypeSubtypeString) > 0;
                    }
                }
            }

            return $hasValidContentType;
        }

        return false;
    }

    /**
     * Get a collection of type/subtype strings that this model allows.
     *
     * Special cases:
     * - empty array => all
     * - null => none
     *
     * @return string[]
     */
    protected static function getAllowedContentTypeStrings()
    {
        return [];
    }

    /**
     * Get a collection of type/subtype string patterns that this model allows.
     *
     * Special cases:
     * - empty array => all
     * - null => none
     *
     * @return string[]
     */
    protected static function getAllowedContentTypePatterns()
    {
        return [];
    }
}
