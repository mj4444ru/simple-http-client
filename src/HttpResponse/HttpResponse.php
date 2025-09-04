<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\HttpResponse;

use Mj4444\SimpleHttpClient\Contracts;
use Mj4444\SimpleHttpClient\Contracts\HttpRequestInterface;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\Http\HttpException;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\UnexpectedContentTypeException;

use function array_key_exists;
use function in_array;
use function is_array;

class HttpResponse implements Contracts\HttpResponseInterface
{
    /**
     * @var lowercase-string|non-empty-array<lowercase-string|null>|null
     */
    protected string|array|null $expectedContentType;

    /**
     * @param array<string, list<string>> $headers
     */
    public function __construct(
        public readonly HttpRequestInterface $request,
        public readonly int $httpCode,
        public readonly string $url,
        public readonly array $headers,
        public readonly ?string $contentType,
        public readonly string $body
    ) {
        $this->expectedContentType = $this->request->getExpectedContentType();
    }

    /**
     * @inheritDoc
     *
     * @throws UnexpectedContentTypeException
     */
    public function checkContentType(string|array|null $expectedContentType = null): void
    {
        $expectedContentType ??= $this->expectedContentType;

        if ($expectedContentType === null) {
            return;
        }

        /** @psalm-suppress RiskyTruthyFalsyComparison */
        $contentType = $this->contentType ? strtolower($this->contentType) : $this->contentType;

        if (is_array($expectedContentType) && in_array($contentType, $expectedContentType, true)) {
            return;
        }

        if ($contentType !== $expectedContentType) {
            throw new UnexpectedContentTypeException($this);
        }
    }

    /**
     * @inheritDoc
     *
     * @throws HttpException
     */
    public function checkHttpCode(int|array $allowedCode = 200): void
    {
        if (is_array($allowedCode)) {
            if (!in_array($this->getHttpCode(), $allowedCode, true)) {
                HttpException::throw($this, $allowedCode);
            }
        } elseif ($allowedCode !== $this->httpCode) {
            HttpException::throw($this, [$allowedCode]);
        }
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    /**
     * @inheritDoc
     */
    public function getData(): mixed
    {
        return null;
    }

    public function getFirstHeader(string $name): ?string
    {
        $headers = $this->getHeaders();

        if (!array_key_exists($name, $headers)) {
            return null;
        }

        return reset($headers[$name]);
    }

    /**
     * @inheritDoc
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    public function getRequest(): HttpRequestInterface
    {
        return $this->request;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
