<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Contracts;

interface HttpRequestInterface
{
    public function getBody(): ?string;

    /**
     * @return non-empty-string[]|null
     */
    public function getHeaders(): ?array;

    /**
     * @return non-empty-string
     */
    public function getMethod(): string;

    /**
     * @return non-empty-string
     */
    public function getUrl(): string;

    public function isResponseHeadersRequired(): bool;
}
