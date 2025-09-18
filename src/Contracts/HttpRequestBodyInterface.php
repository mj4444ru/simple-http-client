<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Contracts;

interface HttpRequestBodyInterface
{
    public function getBody(): string;

    /**
     * @return non-empty-string|null
     */
    public function getBodyContentType(): ?string;
}
