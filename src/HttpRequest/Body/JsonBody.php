<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\HttpRequest\Body;

use JsonException;
use Mj4444\SimpleHttpClient\Contracts\HttpRequestBodyInterface;
use Mj4444\SimpleHttpClient\Exceptions\HttpRequest\JsonEncodeExceptionHttp;

final class JsonBody implements HttpRequestBodyInterface
{
    /**
     * @param non-empty-string|null $contentType
     */
    public function __construct(
        public mixed $data,
        public ?string $contentType = 'application/json; charset=utf-8',
    ) {
    }

    /**
     * @inheritDoc
     *
     * @throws JsonEncodeExceptionHttp
     */
    public function getBody(): string
    {
        try {
            return json_encode($this->data, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new JsonEncodeExceptionHttp($this->data, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function getBodyContentType(): ?string
    {
        return $this->contentType;
    }
}
