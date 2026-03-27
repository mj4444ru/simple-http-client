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
     * @return non-empty-string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @return non-empty-string
     */
    public function getMime(): string
    {
        return $this->mime;
    }

    /**
     * @return non-empty-string
     */
    public function getPostName(): string
    {
        return $this->postName;
    }
}
