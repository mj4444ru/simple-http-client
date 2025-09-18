<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\HttpRequest;

use Mj4444\SimpleHttpClient\HttpResponse\JsonHttpResponse;

/**
 * @extends BaseHttpRequest<JsonHttpResponse>
 */
final class JsonHttpRequest extends BaseHttpRequest
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
     */
    public function makeResponse(
        int $httpCode,
        string $url,
        string $effectiveUrl,
        ?string $redirectUrl,
        array $headers,
        ?string $contentType,
        string $response
    ): JsonHttpResponse {
        return new JsonHttpResponse(
            $this,
            $httpCode,
            $url,
            $effectiveUrl,
            $redirectUrl,
            $headers,
            $contentType,
            $response
        );
    }
}
