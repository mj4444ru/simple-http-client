<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\HttpRequest\Body;

use Mj4444\SimpleHttpClient\Contracts\HttpRequest\BodyInterface;

final class StringBody implements BodyInterface
{
    /**
     * @param non-empty-string|null $contentType
     */
    public function __construct(
        public string $value,
        public ?string $contentType = null
    ) {
    }

    public function getBody(): string
    {
        return $this->value;
    }

    public function getContentType(): ?string
    {
        return $this->contentType;
    }
}
