<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\HttpResponse;

use Mj4444\SimpleHttpClient\HttpRequest\HttpRequestEx;

/**
 * @extends BaseHttpResponse<HttpRequestEx>
 */
final class HttpResponseEx extends BaseHttpResponse
{
    public function getData(): null
    {
        return null;
    }
}
