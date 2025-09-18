<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Contracts;

/**
 * @template TRequest of HttpRequestInterface
 */
interface HttpResponseInterface
{
    /**
     * @param lowercase-string|non-empty-array<lowercase-string|null>|null $expectedContentType
     */
    public function checkContentType(string|array|null $expectedContentType = null): void;

    /**
     * @param int|non-empty-array<int> $allowedCode
     */
    public function checkHttpCode(int|array $allowedCode = 200): void;

    public function getBody(): string;

    public function getContentType(): ?string;

    public function getData(): mixed;

    public function getEffectiveUrl(): string;

    public function getFirstHeader(string $name): ?string;

    /**
     * @return array<string, list<string>>
     */
    public function getHeaders(): array;

    public function getHttpCode(): int;

    public function getRedirectUrl(): ?string;

    /**
     * @return TRequest
     */
    public function getRequest(): HttpRequestInterface;

    public function getUrl(): string;
}
