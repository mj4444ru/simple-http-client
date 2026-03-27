<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Contracts\HttpRequest;

interface FileInterface
{
    /**
     * @return non-empty-string
     */
    public function getFileName(): string;

    /**
     * @return non-empty-string
     */
    public function getMime(): string;

    /**
     * @return non-empty-string
     */
    public function getPostName(): string;
}
