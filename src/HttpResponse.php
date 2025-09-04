<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient;

use Mj4444\SimpleHttpClient\Contracts\HttpRequestInterface;
use Mj4444\SimpleHttpClient\Exceptions\Http\HttpException;

use function array_key_exists;
use function in_array;

final readonly class HttpResponse implements Contracts\HttpResponseInterface
{
    /**
     * @param array<string, list<string>> $headers
     */
    public function __construct(
        private HttpRequestInterface $request,
        private int $httpCode,
        private array $headers,
        private ?string $contentType,
        private string $body
    ) {
    }

    /**
     * @inheritDoc
     */
    public function checkHttpCode(array $allowedCodes = [200]): void
    {
        if ($allowedCodes && !in_array($this->getHttpCode(), $allowedCodes, true)) {
            HttpException::throw($this, $allowedCodes);
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
}
