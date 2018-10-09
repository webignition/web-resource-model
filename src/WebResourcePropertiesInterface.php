<?php

namespace webignition\WebResource;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;

interface WebResourcePropertiesInterface
{
    public static function create(array $args): WebResourcePropertiesInterface;
    public function getUri(): ?UriInterface;
    public function hasUri(): bool ;
    public function getContentType(): ?InternetMediaTypeInterface;
    public function hasContentType(): ?bool;
    public function getContent(): ?string;
    public function hasContent(): ?bool;
    public function getResponse(): ?ResponseInterface;
    public function hasResponse(): ?bool;
}
