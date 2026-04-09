<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Contracts;

use Mj4444\SimpleHttpClient\Contracts\HttpRequest\BodyInterface;

/**
 * Interface representing an HTTP request.
 *
 * @template TResponse of HttpResponseInterface
 */
interface HttpRequestInterface
{
    /**
     * Returns the request body.
     */
    public function getBody(): BodyInterface|null;

    /**
     * Returns the connection timeout.
     *
     * @return non-negative-int|false|null The number of **milliseconds** to wait while trying to connect.
     *     Use `0` to wait indefinitely. Set to `null` to use the client default.
     *     Return `false` to explicitly unset the timeout, meaning any value set on this client will be reset.
     */
    public function getConnectTimeout(): int|false|null;

    /**
     * @return non-empty-string[]
     */
    public function getHeaders(): array;

    /**
     * Returns the maximum number of HTTP redirects to follow.
     *
     * @return int<-1, max>|null The maximum number of redirects, `-1` for unlimited,
     *     or `null` to use the client default.
     */
    public function getMaxRedirects(): ?int;

    /**
     * Returns the HTTP method.
     *
     * @return non-empty-string
     */
    public function getMethod(): string;

    /**
     * Returns the request timeout.
     *
     * @return non-negative-int|false|null The maximum number of **milliseconds** that a request can run.
     *     Use `0` to wait indefinitely. Set to `null` to use the client default.
     *     Return `false` to explicitly unset the timeout, meaning any value set on this client will be reset.
     */
    public function getTimeout(): int|false|null;

    /**
     * Returns the request URL with query string.
     *
     * @return non-empty-string
     */
    public function getUrl(): string;

    /**
     * Returns whether to follow HTTP redirects.
     *
     * @return bool|null `true` to follow redirects, `false` to disable,
     *     or `null` to use the client default.
     */
    public function isFollowLocation(): ?bool;

    /**
     * Returns whether the request method is POST, PUT, or PATCH.
     */
    public function isPost(): bool;

    /**
     * Returns whether response headers should be captured.
     *
     * @return bool|null `true` to capture headers, `false` to ignore them,
     *     or `null` to use the client default.
     */
    public function isResponseHeadersRequired(): ?bool;

    /**
     * Creates an HTTP response for this request.
     *
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
