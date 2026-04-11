<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\HttpRequest\Body;

use Closure;
use Mj4444\SimpleHttpClient\Contracts\HttpRequest\BodyInterface;
use Mj4444\SimpleHttpClient\HttpRequest\Body\BodyReader\FileReader;

final class FileBody implements BodyInterface
{
    /**
     * @param non-empty-string $fileName
     * @param non-empty-string|null $contentType
     * @param non-negative-int $offset
     * @param Closure(non-negative-int $bytesSent, non-negative-int $totalBytes): void|null $progressCallback
     */
    public function __construct(
        public string $fileName,
        public ?string $contentType = null,
        public int $offset = 0,
        public ?int $length = null,
        public ?Closure $progressCallback = null
    ) {
    }

    public function getBody(): FileReader
    {
        return new FileReader($this->fileName, $this->offset, $this->length, $this->progressCallback);
    }

    public function getContentType(): ?string
    {
        return $this->contentType;
    }
}
