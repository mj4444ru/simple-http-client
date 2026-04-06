<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\HttpRequest\Body\MultipartBody;

use Mj4444\SimpleHttpClient\Contracts\HttpRequest\StringFileInterface;

final readonly class StringFile implements StringFileInterface
{
    /**
     * @param string $data
     * @param non-empty-string $postName
     * @param non-empty-string $mime
     */
    public function __construct(
        public string $data,
        public string $postName,
        public string $mime = 'application/octet-stream'
    ) {
    }

    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function getMime(): string
    {
        return $this->mime;
    }

    /**
     * @inheritDoc
     */
    public function getPostName(): string
    {
        return $this->postName;
    }
}
