<?php

namespace webignition\Tests\WebResource\Implementation;

abstract class AbstractSpecificContentTypeWebResourceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return array
     */
    public function createDataProvider()
    {
        $dataSet = $this->modelsDataProvider();

        foreach ($dataSet as $index => $testData) {
            $testData['contentTypeString'] = $testData['contentTypeType'] . '/' .  $testData['contentTypeSubtype'];
            unset($testData['contentTypeType']);
            unset($testData['contentTypeSubtype']);

            $dataSet[$index] = $testData;
        }

        return $dataSet;
    }

    /**
     * @return array
     */
    public function modelsDataProvider()
    {
        return [
            'text/plain' => [
                'contentTypeType' => 'text',
                'contentTypeSubtype' => 'plain',
            ],
            'text/html' => [
                'contentTypeType' => 'text',
                'contentTypeSubtype' => 'html',
            ],
            'application/xml' => [
                'contentTypeType' => 'application',
                'contentTypeSubtype' => 'xml',
            ],
            'application/json' => [
                'contentTypeType' => 'application',
                'contentTypeSubtype' => 'json',
            ],
            'image/png' => [
                'contentTypeType' => 'image',
                'contentTypeSubtype' => 'png',
            ],
        ];
    }
}
