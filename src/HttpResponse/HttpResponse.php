<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\HttpResponse;

use Mj4444\SimpleHttpClient\HttpRequest\HttpRequest;

/**
 * @extends BaseHttpResponse<HttpRequest>
 */
final class HttpResponse extends BaseHttpResponse
{
    public function getData(): null
    {
        return null;
    }
}
