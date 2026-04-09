<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient;

use Mj4444\SimpleHttpClient\Contracts\HttpClientExInterface;

abstract class BaseHttpClient implements HttpClientExInterface
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

    public function setFollowLocation(bool $followLocation): static
    {
        $this->followLocation = $followLocation;

        return $this;
    }

    public function setHeader(string|int $index, string $header): static
    {
        $this->headers[$index] = $header;

        return $this;
    }

    public function setHeaders(array $headers): static
    {
        $this->headers = $headers;

        return $this;
    }

    public function setMaxRedirects(int $maxRedirects): static
    {
        $this->maxRedirects = $maxRedirects;

        return $this;
    }

    public function setResponseHeadersRequired(bool $value): static
    {
        $this->responseHeadersRequired = $value;

        return $this;
    }
}
