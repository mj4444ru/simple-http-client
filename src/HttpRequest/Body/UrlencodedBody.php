<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\HttpRequest\Body;

use Mj4444\SimpleHttpClient\Contracts\HttpRequestBodyInterface;

final class UrlencodedBody implements HttpRequestBodyInterface
{
    /**
     * @param non-empty-string|null $contentType
     */
    public function __construct(
        public array|object $data,
        public ?string $contentType = 'application/x-www-form-urlencoded'
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getBody(): string
    {
        return http_build_query($this->data);
    }

    /**
     * @inheritDoc
     */
    public function getBodyContentType(): ?string
    {
        return $this->contentType;
    }
}
