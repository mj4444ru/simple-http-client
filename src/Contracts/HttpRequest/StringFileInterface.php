<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Contracts\HttpRequest;

interface StringFileInterface
{
    public function getData(): string;

    /**
     * @return non-empty-string
     */
    public function getMime(): string;

    /**
     * @return non-empty-string
     */
    public function getPostName(): string;
}
