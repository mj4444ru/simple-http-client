<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Contracts;

use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\Http\HttpException;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\ParseDataException;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\UnexpectedContentTypeException;

interface HttpResponseInterface
{
    /**
     * @param lowercase-string|non-empty-array<lowercase-string|null>|null $expectedContentType
     * @throws UnexpectedContentTypeException
     */
    public function checkContentType(string|array|null $expectedContentType = null): void;

    /**
     * @param int|non-empty-array<int> $allowedCode
     *
     * @throws HttpException
     */
    public function checkHttpCode(int|array $allowedCode = 200): void;

    public function getBody(): string;

    public function getContentType(): ?string;

    /**
     * @throws UnexpectedContentTypeException
     * @throws ParseDataException
     */
    public function getData(): mixed;

    public function getFirstHeader(string $name): ?string;

    /**
     * @return array<string, list<string>>
     */
    public function getHeaders(): array;

    public function getHttpCode(): int;

    public function getRequest(): HttpRequestInterface;

    public function getUrl(): string;
}
