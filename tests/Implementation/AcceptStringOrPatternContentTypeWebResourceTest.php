<?php

namespace webignition\Tests\WebResource\Implementation;

use webignition\InternetMediaType\InternetMediaType;
use webignition\InternetMediaType\Parser\ParseException as InternetMediaTypeParseException;
use webignition\WebResource\Exception\InvalidContentTypeException;
use webignition\WebResource\TestingTools\ResponseFactory;

class AcceptStringOrPatternContentTypeWebResourceTest extends AbstractSpecificContentTypeWebResourceTest
{
    /**
     * @dataProvider createSuccessDataProvider
     *
     * @param $contentTypeString
     *
     * @throws InternetMediaTypeParseException
     * @throws InvalidContentTypeException
     */
    public function testCreateSuccess($contentTypeString)
    {
        $response = ResponseFactory::create($contentTypeString);

        new AcceptStringOrPatternContentTypeWebResource($response);
    }

    /**
     * @return array
     */
    public function createSuccessDataProvider()
    {
        $dataSet = $this->createDataProvider();

        unset($dataSet['image/png']);

        return $dataSet;
    }

    /**
     * @dataProvider createFailureDataProvider
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

        new AcceptStringOrPatternContentTypeWebResource($response);
    }

    /**
     * @return array
     */
    public function createFailureDataProvider()
    {
        $dataSet = $this->createDataProvider();

        unset($dataSet['application/xml']);
        unset($dataSet['application/json']);
        unset($dataSet['text/plain']);
        unset($dataSet['text/html']);

        return $dataSet;
    }

    /**
     * @dataProvider specificModelsDataProvider
     *
     * @param string $contentTypeType
     * @param string $contentTypeSubtype
     * @param bool $expectedModels
     */
    public function testModels($contentTypeType, $contentTypeSubtype, $expectedModels)
    {
        $contentType = new InternetMediaType();
        $contentType->setType($contentTypeType);
        $contentType->setSubtype($contentTypeSubtype);

        $this->assertEquals($expectedModels, AcceptStringOrPatternContentTypeWebResource::models($contentType));
    }

    /**
     * @return array
     */
    public function specificModelsDataProvider()
    {
        return array_merge_recursive(parent::modelsDataProvider(), [
            'text/plain' => [
                'expectedModels' => true,
            ],
            'text/html' => [
                'expectedModels' => true,
            ],
            'application/xml' => [
                'expectedModels' => true,
            ],
            'application/json' => [
                'expectedModels' => true,
            ],
            'image/png' => [
                'expectedModels' => false,
            ],
        ]);
    }
}
