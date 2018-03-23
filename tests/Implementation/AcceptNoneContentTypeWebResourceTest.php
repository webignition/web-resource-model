<?php

namespace webignition\Tests\WebResource\Implementation;

use webignition\InternetMediaType\InternetMediaType;
use webignition\InternetMediaType\Parser\ParseException as InternetMediaTypeParseException;
use webignition\Tests\WebResource\Factory\ResponseFactory;
use webignition\WebResource\Exception\InvalidContentTypeException;

class AcceptNoneContentTypeWebResourceTest extends AbstractSpecificContentTypeWebResourceTest
{
    /**
     * @dataProvider createDataProvider
     *
     * @param $contentTypeString
     *
     * @throws InternetMediaTypeParseException
     * @throws InvalidContentTypeException
     */
    public function testCreateFailure($contentTypeString)
    {
        $response = ResponseFactory::create($contentTypeString);

        $this->expectException(InvalidContentTypeException::class);
        $this->expectExceptionCode(InvalidContentTypeException::CODE);
        $this->expectExceptionMessage(sprintf(InvalidContentTypeException::MESSAGE, $contentTypeString));

        new AcceptNoneContentTypeWebResource($response);
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

        $this->assertFalse(AcceptNoneContentTypeWebResource::models($contentType));
    }
}
