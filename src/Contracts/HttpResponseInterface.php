<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Contracts;

use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\Http\HttpException;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\UnexpectedContentTypeException;

/**
 * Interface representing an HTTP response.
 *
 * @template TRequest of HttpRequestInterface
 */
interface HttpResponseInterface
{
    /**
     * Validates the response `Content-Type` header against expected values.
     *
     * @param lowercase-string|non-empty-array<lowercase-string|null>|null $expectedContentType
     * @throws UnexpectedContentTypeException
     */
    public function checkContentType(string|array|null $expectedContentType = null): void;

    /**
     * Validates the HTTP status code against allowed values.
     *
     * @param int|non-empty-array<int> $allowedCode
     * @throws HttpException
     */
    public function checkHttpCode(int|array $allowedCode = 200): void;

    /**
     * Returns the response body as a string.
     */
    public function getBody(): string;

    /**
     * Returns the `Content-Type` header value, or `null` if not present.
     */
    public function getContentType(): ?string;

    /**
     * Returns parsed response data (e.g. decoded JSON), or `null` if not applicable.
     */
    public function getData(): mixed;

    /**
     * Returns the final URL after any redirects.
     */
    public function getEffectiveUrl(): string;

    /**
     * Returns the first value of a response header, or `null` if not present.
     */
    public function getFirstHeader(string $name): ?string;

    /**
     * Returns all response headers as an associative array.
     *
     * @return array<string, list<string>>
     */
    public function getHeaders(): array;

    /**
     * Returns the HTTP status code.
     */
    public function getHttpCode(): int;

    /**
     * Returns the redirect URL if the response indicates a redirect, or `null`.
     */
    public function getRedirectUrl(): ?string;

    /**
     * Returns the original request that produced this response.
     *
     * @return TRequest
     */
    public function getRequest(): HttpRequestInterface;

    /**
     * Returns the original request URL.
     */
    public function getUrl(): string;
}
