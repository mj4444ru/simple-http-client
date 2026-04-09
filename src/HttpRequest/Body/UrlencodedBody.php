<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\HttpRequest\Body;

use Mj4444\SimpleHttpClient\Contracts\HttpRequest\BodyInterface;

final class UrlencodedBody implements BodyInterface
{
    /**
     * @param non-empty-string|null $contentType
     */
    public function __construct(
        public array|object $data,
        public ?string $contentType = 'application/x-www-form-urlencoded'
    ) {
    }

    public function getBody(): string
    {
        return http_build_query($this->data);
    }

    public function getContentType(): ?string
    {
        return $this->contentType;
    }
}
