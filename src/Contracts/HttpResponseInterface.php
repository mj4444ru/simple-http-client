<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Contracts;

interface HttpResponseInterface
{
    /**
     * @param list<int> $allowedCodes
     */
    public function checkHttpCode(array $allowedCodes = [200]): void;

    public function getBody(): string;

    public function getContentType(): ?string;

    public function getFirstHeader(string $name): ?string;

    /**
     * @return array<string, list<string>>
     */
    public function getHeaders(): array;

    public function getHttpCode(): int;

    public function getRequest(): HttpRequestInterface;
}
