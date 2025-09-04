<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Contracts;

use Mj4444\SimpleHttpClient\Exceptions\HttpRequest\BodyRequiredException;

interface HttpRequestInterface
{
    public function getBody(): ?string;

    /**
     * @return lowercase-string|non-empty-array<lowercase-string|null>|null
     */
    public function getExpectedContentType(): string|array|null;

    /**
     * @return non-empty-string[]
     */
    public function getHeaders(?bool $isPostRequest = null): array;

    /**
     * @return non-empty-string
     */
    public function getMethod(): string;

    /**
     * @return non-empty-string
     */
    public function getUrl(): string;

    public function isResponseHeadersRequired(): ?bool;

    /**
     * @param array<string, list<string>> $headers
     */
    public function makeResponse(
        int $httpCode,
        string $url,
        array $headers,
        ?string $contentType,
        string $response
    ): HttpResponseInterface;
}
