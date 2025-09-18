<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\HttpRequest;

enum HttpMethod: string
{
    case Get = 'GET';
    case Post = 'POST';
    case Put = 'PUT';
    case Patch = 'PATCH';
    case Delete = 'DELETE';
}
