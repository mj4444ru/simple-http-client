<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\HttpResponse;

use Mj4444\SimpleHttpClient\Contracts\HttpResponseInterface;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\Http\HttpException;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\UnexpectedContentTypeException;
use Mj4444\SimpleHttpClient\HttpRequest\BaseHttpRequest;

use function array_key_exists;
use function in_array;
use function is_array;

/**
 * @template TRequest of BaseHttpRequest
 * @implements HttpResponseInterface<TRequest>
 */
abstract class BaseHttpResponse implements HttpResponseInterface
{
    protected bool $contentTypeValidated = false;

    /**
     * @param TRequest $request
     * @param array<string, list<string>> $headers
     */
    public function __construct(
        public readonly BaseHttpRequest $request,
        public readonly int $httpCode,
        public readonly string $url,
        public readonly string $effectiveUrl,
        public readonly ?string $redirectUrl,
        public readonly array $headers,
        public readonly ?string $contentType,
        public readonly string $body
    ) {
    }

    /**
     * @inheritDoc
     *
     * @throws UnexpectedContentTypeException
     */
    public function checkContentType(string|array|null $expectedContentType = null): void
    {
        if ($expectedContentType !== null || !$this->contentTypeValidated) {
            $expectedContentType ??= $this->request->expectedContentType;

            if ($expectedContentType !== null) {
                $contentType = $this->contentType !== null ? strtolower($this->contentType) : null;

                if (is_array($expectedContentType)) {
                    if (!in_array($contentType, $expectedContentType, true)) {
                        throw new UnexpectedContentTypeException($this);
                    }
                } elseif ($contentType !== $expectedContentType) {
                    throw new UnexpectedContentTypeException($this);
                }
            }

            $this->contentTypeValidated = true;
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

    public function getEffectiveUrl(): string
    {
        return $this->effectiveUrl;
    }

    public function getFirstHeader(string $name): ?string
    {
        $headers = $this->getHeaders();

        if (!array_key_exists($name, $headers)) {
            return null;
        }

        $result = reset($headers[$name]);

        return $result !== false ? $result : null;
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

    public function getRedirectUrl(): ?string
    {
        return $this->redirectUrl;
    }

    /**
     * @return TRequest
     */
    public function getRequest(): BaseHttpRequest
    {
        return $this->request;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
