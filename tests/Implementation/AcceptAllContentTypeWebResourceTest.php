<?php

namespace webignition\Tests\WebResource\Implementation;

use webignition\InternetMediaType\InternetMediaType;
use webignition\InternetMediaType\Parser\ParseException as InternetMediaTypeParseException;
use webignition\WebResource\Exception\InvalidContentTypeException;
use webignition\WebResource\TestingTools\ResponseFactory;

class AcceptAllContentTypeWebResourceTest extends AbstractSpecificContentTypeWebResourceTest
{
    /**
     * @dataProvider createDataProvider
     *
     * @param $contentTypeString
     *
     * @throws InternetMediaTypeParseException
     * @throws InvalidContentTypeException
     */
    public function testCreateSuccess($contentTypeString)
    {
        $response = ResponseFactory::create($contentTypeString);

        new AcceptAllContentTypeWebResource($response);
    }

    /**
     * @dataProvider modelsDataProvider
     *
     * @param string $contentTypeType
     * @param string $contentTypeSubtype
     */
    public function testModels($contentTypeType, $contentTypeSubtype)
    {
        $contentType = new InternetMediaType();
        $contentType->setType($contentTypeType);
        $contentType->setSubtype($contentTypeSubtype);

        $this->assertTrue(AcceptAllContentTypeWebResource::models($contentType));
    }
}
