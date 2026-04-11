<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\HttpRequest\Body;

use Closure;
use Mj4444\SimpleHttpClient\Contracts\HttpRequest\BodyInterface;
use Mj4444\SimpleHttpClient\HttpRequest\Body\BodyReader\StreamReader;

final class StreamBody implements BodyInterface
{
    /**
     * @param resource $resource
     * @param non-empty-string|null $contentType
     * @param non-negative-int|null $offset
     * @param Closure(non-negative-int $bytesSent, non-negative-int $totalBytes): void|null $progressCallback
     */
    public function __construct(
        public mixed $resource,
        public ?string $contentType = null,
        public ?int $offset = null,
        public ?int $length = null,
        public ?Closure $progressCallback = null
    ) {
    }

    public function getBody(): StreamReader
    {
        return new StreamReader($this->resource, $this->offset, $this->length, $this->progressCallback);
    }

    public function getContentType(): ?string
    {
        return $this->contentType;
    }
}
