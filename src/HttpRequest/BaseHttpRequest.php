<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\HttpRequest;

use Mj4444\SimpleHttpClient\Contracts\HttpRequestBodyInterface;
use Mj4444\SimpleHttpClient\Contracts\HttpRequestInterface;
use Mj4444\SimpleHttpClient\Contracts\HttpResponseInterface;
use Mj4444\SimpleHttpClient\HttpRequest\Body\JsonBody;
use Mj4444\SimpleHttpClient\HttpRequest\Body\NoBody;
use Mj4444\SimpleHttpClient\HttpRequest\Body\UrlencodedBody;
use Mj4444\SimpleHttpClient\HttpResponse\BaseHttpResponse;

use function in_array;
use function is_object;

/**
 * @api
 * @template TResponse of BaseHttpResponse
 * @implements HttpRequestInterface<TResponse>
 */
abstract class BaseHttpRequest implements HttpRequestInterface
{
    /**
     * @var non-empty-string|null
     */
    public ?string $accept = null;
    public string|HttpRequestBodyInterface|null $body = null;
    /**
     * @var non-empty-string|null
     */
    public ?string $contentType = null;
    /**
     * @var lowercase-string|non-empty-array<lowercase-string|null>|null
     */
    public string|array|null $expectedContentType = null;
    public ?bool $followLocation = null;
    /**
     * @var non-empty-string[]
     */
    public array $headers = [];
    /**
     * @var int<-1, max>|null
     */
    public ?int $maxRedirects = null;
    public ?bool $responseHeadersRequired = null;

    /**
     * @param non-empty-string $url
     * @param array<string|int|list<string|int>>|null $query
     */
    public function __construct(
        public string $url,
        public ?array $query = null,
        public HttpMethod $method = HttpMethod::Get
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
        return is_object($this->body)
            ? $this->body->getBody()
            : $this->body;
    }

    /**
     * @inheritDoc
     */
    public function getHeaders(): array
    {
        $headers = array_filter($this->headers);

        /** @psalm-suppress RedundantCondition */
        if ($this->accept !== null && $this->accept !== '') {
            $headers[] = 'Accept: ' . $this->accept;
        }

        if ($this->isPost()) {
            $contentType = $this->body instanceof HttpRequestBodyInterface
                ? $this->body->getBodyContentType()
                : $this->contentType;

            /** @psalm-suppress RedundantCondition */
            if ($contentType !== null && $contentType !== '') {
                $headers[] = 'Content-Type: ' . $contentType;
            }
        }

        return $headers;
    }

    /**
     * @inheritDoc
     */
    public function getMaxRedirects(): ?int
    {
        return $this->maxRedirects;
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

    public function isFollowLocation(): ?bool
    {
        return $this->followLocation;
    }

    public function isPost(): bool
    {
        return in_array($this->method, [HttpMethod::Post, HttpMethod::Put, HttpMethod::Patch], true);
    }

    public function isResponseHeadersRequired(): ?bool
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
    public function setBody(string|HttpRequestBodyInterface|null $body): static
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
     * @param lowercase-string|non-empty-array<lowercase-string|null>|null $expectedContentType
     * @return $this
     */
    public function setExpectedContentType(array|string|null $expectedContentType): static
    {
        $this->expectedContentType = $expectedContentType;

        return $this;
    }

    /**
     * @return $this
     */
    public function setFollowLocation(?bool $followLocation): static
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
     * @param non-empty-string|null $contentType
     * @return $this
     */
    public function setJsonBody(mixed $data, ?string $contentType = 'application/json; charset=utf-8'): static
    {
        return $this->setBody(new JsonBody($data, $contentType));
    }

    /**
     * @param int<-1, max>|null $maxRedirects
     * @return $this
     */
    public function setMaxRedirects(?int $maxRedirects): static
    {
        $this->maxRedirects = $maxRedirects;

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
     * @return $this
     */
    public function setNoBody(): static
    {
        return $this->setBody(new NoBody());
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
    public function setReferer(string|HttpRequestInterface|HttpResponseInterface $referer): static
    {
        if ($referer instanceof HttpRequestInterface) {
            $referer = $referer->getUrl();
        } elseif ($referer instanceof HttpResponseInterface) {
            $referer = $referer->getEffectiveUrl();
        }

        $this->setHeader('referer', 'Referer: ' . $referer);

        return $this;
    }

    /**
     * @return $this
     */
    public function setResponseHeadersRequired(?bool $value = true): static
    {
        $this->responseHeadersRequired = $value;

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
     * @param non-empty-string|null $contentType
     * @return $this
     */
    public function setUrlencodedBody(
        object|array $data,
        ?string $contentType = 'application/x-www-form-urlencoded'
    ): static {
        return $this->setBody(new UrlencodedBody($data, $contentType));
    }
}
