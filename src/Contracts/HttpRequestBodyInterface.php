<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Contracts;

use Mj4444\SimpleHttpClient\Exceptions\HttpRequest\HttpRequestException;

interface HttpRequestBodyInterface
{
    /**
     * @throws HttpRequestException
     */
    public function getBody(): string;

    /**
     * @return non-empty-string|null
     */
    public function getBodyContentType(): ?string;
}
