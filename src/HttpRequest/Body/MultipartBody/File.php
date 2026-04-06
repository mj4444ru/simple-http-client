<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\HttpRequest\Body\MultipartBody;

use Mj4444\SimpleHttpClient\Contracts\HttpRequest\FileInterface;

final readonly class File implements FileInterface
{
    /**
     * @param non-empty-string $fileName
     * @param non-empty-string $postName
     * @param non-empty-string $mime
     */
    public function __construct(
        public string $fileName,
        public string $postName,
        public string $mime = 'application/octet-stream'
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getFileName(): string
    {
        return $this->fileName;
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
