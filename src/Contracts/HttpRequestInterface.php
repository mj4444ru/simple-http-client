<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Contracts;

/**
 * @template TResponse of HttpResponseInterface
 */
interface HttpRequestInterface
{
    public function getBody(): ?string;

    /**
     * @return non-empty-string[]
     */
    public function getHeaders(): array;

    /**
     * @return int<-1, max>|null
     */
    public function getMaxRedirects(): ?int;

    /**
     * @return non-empty-string
     */
    public function getMethod(): string;

    /**
     * @return non-empty-string
     */
    public function getUrl(): string;

    public function isFollowLocation(): ?bool;

    public function isPost(): bool;

    public function isResponseHeadersRequired(): ?bool;

    /**
     * @param array<string, list<string>> $headers
     * @return TResponse
     */
    public function makeResponse(
        int $httpCode,
        string $url,
        string $effectiveUrl,
        ?string $redirectUrl,
        array $headers,
        ?string $contentType,
        string $response
    ): HttpResponseInterface;
}
