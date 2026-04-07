<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\HttpRequest\Body;

use Mj4444\SimpleHttpClient\Contracts\HttpRequest\BodyInterface;
use Mj4444\SimpleHttpClient\HttpRequest\Body\BodyReader\StringReader;

final class StringStreamBody implements BodyInterface
{
    /**
     * @param non-empty-string|null $contentType
     * @param non-negative-int $offset
     */
    public function __construct(
        public string $content,
        public ?string $contentType = null,
        public int $offset = 0,
        public ?int $length = null
    ) {
    }

    public function getBody(): StringReader
    {
        return new StringReader($this->content, $this->offset, $this->length);
    }

    /**
     * @inheritDoc
     */
    public function getContentType(): ?string
    {
        return $this->contentType;
    }
}
