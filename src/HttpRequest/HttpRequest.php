<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\HttpRequest;

use Mj4444\SimpleHttpClient\HttpResponse\HttpResponse;

/**
 * @extends BaseHttpRequest<HttpResponse>
 */
final class HttpRequest extends BaseHttpRequest
{
    /**
     * @inheritDoc
     */
    public function makeResponse(
        int $httpCode,
        string $url,
        string $effectiveUrl,
        array $headers,
        ?string $contentType,
        string $response
    ): HttpResponse {
        return new HttpResponse($this, $httpCode, $url, $effectiveUrl, $headers, $contentType, $response);
    }
}
