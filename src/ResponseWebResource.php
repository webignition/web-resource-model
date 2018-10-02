<?php

namespace webignition\WebResource;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use webignition\InternetMediaType\Parser\ParseException as InternetMediaTypeParseException;
use webignition\InternetMediaType\Parser\Parser as InternetMediaTypeParser;
use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;
use webignition\WebResource\Exception\ReadOnlyResponseException;
use webignition\WebResource\Exception\UnseekableResponseException;
use webignition\WebResourceInterfaces\ResponseResourceInterface;
use webignition\WebResourceInterfaces\WebResourceInterface;

class ResponseWebResource extends WebResource implements ResponseResourceInterface
{
    const HEADER_CONTENT_TYPE = 'content-type';

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @param UriInterface $uri
     * @param ResponseInterface $response
     *
     * @throws InternetMediaTypeParseException
     */
    public function __construct(UriInterface $uri, ResponseInterface $response)
    {
        $this->response = $response;
        $contentType = $this->createContentTypeFromResponse($response);

        parent::__construct($uri, $contentType, '');
    }

    /**
     * @param string $content
     * @return WebResourceInterface|ResponseWebResource
     *
     * @throws ReadOnlyResponseException
     * @throws UnseekableResponseException
     */
    public function setContent(string $content): WebResourceInterface
    {
        $responseBody = $this->response->getBody();

        if (!$responseBody->isWritable()) {
            throw new ReadOnlyResponseException();
        }

        if (!$responseBody->isSeekable()) {
            throw new UnseekableResponseException();
        }

        $updatedResponseBody = clone $responseBody;

        $updatedResponseBody->rewind();
        $updatedResponseBody->write($content);
        $updatedResponseBody->rewind();

        $updatedResponse = $this->response->withBody($updatedResponseBody);

        return $this->createNewInstance($this->uri, $updatedResponse);
    }

    public function getContent(): string
    {
        $resourceContent = (string)$this->response->getBody();
        $content = @gzdecode($resourceContent);

        if (false === $content) {
            $content = $resourceContent;
        }

        return $content;
    }

    public function setResponse(ResponseInterface $response): ResponseResourceInterface
    {
        return $this->createNewInstance($this->uri, $response);
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function setBody(StreamInterface $body): ResponseResourceInterface
    {
        return $this->createNewInstance($this->uri, $this->response->withBody($body));
    }

    public function getBody(): StreamInterface
    {
        return $this->response->getBody();
    }

    /**
     * @param ResponseInterface $response
     *
     * @return InternetMediaTypeInterface
     *
     * @throws InternetMediaTypeParseException
     */
    private function createContentTypeFromResponse(ResponseInterface $response): InternetMediaTypeInterface
    {
        $internetMediaTypeParser = new InternetMediaTypeParser();
        $internetMediaTypeParserConfiguration = $internetMediaTypeParser->getConfiguration();
        $internetMediaTypeParserConfiguration->enableIgnoreInvalidAttributes();
        $internetMediaTypeParserConfiguration->enableAttemptToRecoverFromInvalidInternalCharacter();

        return $internetMediaTypeParser->parse($response->getHeaderLine(self::HEADER_CONTENT_TYPE));
    }

    private function createNewInstance(UriInterface $uri, ResponseInterface $response): ResponseWebResource
    {
        $className = get_class($this);

        return new $className($uri, $response);
    }
}
