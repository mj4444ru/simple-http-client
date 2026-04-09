<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Contracts;

/**
 * Extended HTTP client interface with additional configuration methods.
 */
interface HttpClientExInterface extends HttpClientInterface
{
    /**
     * Sets the connection timeout.
     *
     * @param non-negative-int|null $connectTimeout The number of **milliseconds** to wait while trying to connect.
     *     Use `0` to wait indefinitely. Set to `null` to use the client default.
     * @return $this
     */
    public function setConnectTimeout(?int $connectTimeout): static;

    /**
     * Enables or disables following HTTP redirects.
     *
     * @param bool $followLocation Set to `true` to follow any `Location:` header that the server sends as part
     *     of the HTTP response. Set to `false` to disable redirect following.
     * @return $this
     */
    public function setFollowLocation(bool $followLocation): static;

    /**
     * Sets a single HTTP header by index.
     *
     * @param string|int $index The header index (string key or numeric position).
     * @param non-empty-string $header The header string (e.g. `Content-Type: application/json`).
     * @return $this
     */
    public function setHeader(string|int $index, string $header): static;

    /**
     * Replaces all HTTP headers.
     *
     * @param non-empty-string[] $headers The headers array.
     * @return $this
     */
    public function setHeaders(array $headers): static;

    /**
     * Sets the maximum number of HTTP redirects to follow.
     *
     * @param int<-1, max> $maxRedirects The maximum amount of HTTP redirections to follow.
     *     Use `-1` for unlimited.
     * @return $this
     */
    public function setMaxRedirects(int $maxRedirects): static;

    /**
     * Enables a proxy for requests.
     *
     * @param string $proxyString A string with the HTTP proxy to tunnel requests through. This should be the hostname,
     *     the dotted numerical IP address or a numerical IPv6 address written within [brackets].
     *     Empty string will disable proxy.
     * @param bool $socks5 Whether to use SOCKS5 proxy instead of HTTP proxy.
     * @return $this
     */
    public function setProxy(string $proxyString = '', bool $socks5 = false): static;

    /**
     * Sets the Referer header.
     *
     * @param string|null $referer A string with the contents of the `Referer:` header to be used in a HTTP request.
     *     Set to `null` to remove the header.
     * @return $this
     */
    public function setReferer(?string $referer): static;

    /**
     * Enables or disables parsing of response headers.
     *
     * @param bool $value Set to `true` to capture returned HTTP headers.
     *     Set to `false` to ignore response headers.
     * @return $this
     */
    public function setResponseHeadersRequired(bool $value): static;

    /**
     * Sets the request timeout.
     *
     * @param non-negative-int|null $timeout The maximum number of **milliseconds** that a request can run.
     *     Use `0` to wait indefinitely. Set to `null` to use the client default.
     * @return $this
     */
    public function setTimeout(?int $timeout): static;

    /**
     * Sets the User-Agent header.
     *
     * @param non-empty-string|null $userAgent The contents of the `User-Agent:` header to be used in a HTTP request.
     *     Set to `null` to remove the header.
     * @return $this
     */
    public function setUserAgent(?string $userAgent): static;
}
