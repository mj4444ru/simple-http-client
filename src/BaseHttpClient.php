<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient;

use Mj4444\SimpleHttpClient\Contracts\HttpClientInterface;

/**
 * @api
 */
abstract class BaseHttpClient implements HttpClientInterface
{
    public bool $followLocation = false;
    /**
     * @var non-empty-string[]
     */
    public array $headers = [];
    /**
     * @var int<-1, max>
     */
    public int $maxRedirects = 20;
    public bool $responseHeadersRequired = false;

    /**
     * @param positive-int|null $connectTimeout
     * @return $this
     */
    abstract public function setConnectTimeout(?int $connectTimeout): static;

    /**
     * @return $this
     */
    public function setFollowLocation(bool $followLocation): static
    {
        $this->followLocation = $followLocation;

        return $this;
    }

    /**
     * @param non-empty-string $header
     * @return $this
     */
    public function setHeader(string|int $index, string $header): static
    {
        $this->headers[$index] = $header;

        return $this;
    }

    /**
     * @param non-empty-string[] $headers
     * @return $this
     */
    public function setHeaders(array $headers): static
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @param int<-1, max> $maxRedirects
     * @return $this
     */
    public function setMaxRedirects(int $maxRedirects): static
    {
        $this->maxRedirects = $maxRedirects;

        return $this;
    }

    abstract public function setReferer(?string $referer): static;

    public function setResponseHeadersRequired(bool $value): void
    {
        $this->responseHeadersRequired = $value;
    }

    /**
     * @param positive-int|null $timeout
     * @return $this
     */
    abstract public function setTimeout(?int $timeout): static;

    abstract public function setUserAgent(?string $userAgent): static;
}
