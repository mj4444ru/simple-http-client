<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\HttpRequest;

use Mj4444\SimpleHttpClient\HttpResponse\HttpResponse;
use Mj4444\SimpleHttpClient\HttpResponse\JsonHttpResponse;

class JsonHttpRequest extends HttpRequest
{
    /**
     * @var non-empty-string|null
     */
    public ?string $accept = 'application/json; charset=utf-8';
    /**
     * @var non-empty-string|null
     */
    public ?string $contentType = 'application/json; charset=utf-8';
    /**
     * @var lowercase-string|non-empty-array<lowercase-string|null>|null
     */
    public string|array|null $expectedContentType = [
        'application/json; charset=utf-8',
        'application/json;charset=utf-8',
        'application/json',
    ];

    /**
     * @inheritDoc
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function makeResponse(
        int $httpCode,
        string $url,
        array $headers,
        ?string $contentType,
        string $response
    ): HttpResponse {
        return new JsonHttpResponse($this, $httpCode, $url, $headers, $contentType, $response);
    }
}
