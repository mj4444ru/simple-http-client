<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient;

use Mj4444\SimpleHttpClient\Contracts\HttpMethod;
use Mj4444\SimpleHttpClient\Contracts\HttpRequestInterface;
use Mj4444\SimpleHttpClient\Exceptions\JsonHttpClient\BodyRequiredException;

use function in_array;

class HttpRequest implements HttpRequestInterface
{
    protected ?string $accept = null;
    protected ?string $body = null;
    protected ?string $contentType = null;
    /**
     * @var non-empty-string[]|null
     */
    protected ?array $headers = null;
    protected bool $responseHeadersRequired = false;

    /**
     * @param non-empty-string $url
     * @param array<string|int|list<string|int>>|null $query
     */
    public function __construct(
        protected string $url,
        protected ?array $query = null,
        protected HttpMethod $method = HttpMethod::Get
    ) {
    }

    /**
     * @param non-empty-string $header
     * @return $this
     */
    public function addHeader(string $header): static
    {
        $this->headers[] = $header;

        return $this;
    }

    public function getBody(): ?string
    {
        if (!in_array($this->method, [HttpMethod::Post, HttpMethod::Put, HttpMethod::Patch], true)) {
            return null;
        }

        if ($this->body === null) {
            throw new BodyRequiredException($this);
        }

        return $this->body;
    }

    /**
     * @inheritDoc
     */
    public function getHeaders(): ?array
    {
        $headers = $this->headers;

        /** @psalm-suppress RiskyTruthyFalsyComparison */
        if ($this->accept) {
            $headers[] = 'Accept: ' . $this->accept;
        }
        /** @psalm-suppress RiskyTruthyFalsyComparison */
        if ($this->contentType) {
            $headers[] = 'Content-Type: ' . $this->contentType;
        }

        return $headers;
    }

    /**
     * @inheritDoc
     */
    public function getMethod(): string
    {
        return $this->method->value;
    }

    /**
     * @inheritDoc
     */
    public function getUrl(): string
    {
        $url = $this->url;

        /** @psalm-suppress RiskyTruthyFalsyComparison */
        if ($this->query) {
            if (str_contains($this->url, '?')) {
                $url .= '&' . http_build_query($this->query);
            } else {
                $url .= '?' . http_build_query($this->query);
            }
        }

        return $url;
    }

    public function isResponseHeadersRequired(): bool
    {
        return $this->responseHeadersRequired;
    }

    /**
     * @param non-empty-string|null $accept
     * @return $this
     */
    public function setAccept(?string $accept): static
    {
        $this->accept = $accept;

        return $this;
    }

    /**
     * @return $this
     */
    public function setBody(?string $body): static
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @param non-empty-string|null $contentType
     * @return $this
     */
    public function setContentType(?string $contentType): static
    {
        $this->contentType = $contentType;

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
     * @param non-empty-string[]|null $headers
     * @return $this
     */
    public function setHeaders(?array $headers): static
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @return $this
     */
    public function setMethod(HttpMethod $method): static
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @param array<string|int|list<string|int>>|null $query
     * @return $this
     */
    public function setQuery(?array $query): static
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @return $this
     */
    public function setResponseHeadersRequired(bool $responseHeadersRequired = true): static
    {
        $this->responseHeadersRequired = $responseHeadersRequired;

        return $this;
    }

    /**
     * @param non-empty-string $url
     * @return $this
     */
    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return $this
     */
    public function setWwwFormUrlencodedBody(object|array $data): static
    {
        $this->setBody(http_build_query($data))
            ->setContentType('application/x-www-form-urlencoded');

        return $this;
    }
}
