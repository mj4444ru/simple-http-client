<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\HttpRequest\Body;

use JsonException;
use Mj4444\SimpleHttpClient\Contracts\HttpRequest\BodyInterface;
use Mj4444\SimpleHttpClient\Exceptions\HttpRequest\JsonEncodeException;

final class JsonBody implements BodyInterface
{
    public static int $encodeFlags = JSON_UNESCAPED_UNICODE;

    /**
     * @param non-empty-string|null $contentType
     */
    public function __construct(
        public mixed $data,
        public ?string $contentType = 'application/json; charset=utf-8',
    ) {
    }

    /**
     * @throws JsonEncodeException
     */
    public function getBody(): string
    {
        try {
            /** @var string */
            return json_encode($this->data, self::$encodeFlags | JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new JsonEncodeException($this->data, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function getContentType(): ?string
    {
        return $this->contentType;
    }
}
