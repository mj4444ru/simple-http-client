<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\HttpRequest;

/**                                                                                                                                                                                             │
 * HTTP request methods.                                                                                                                                                                        │
 */
enum HttpMethod: string
{
    case Delete = 'DELETE';
    case Get = 'GET';
    case Head = 'HEAD';
    case Options = 'OPTIONS';
    case Patch = 'PATCH';
    case Post = 'POST';
    case Put = 'PUT';
}
