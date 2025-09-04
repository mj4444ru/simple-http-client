<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Exceptions\HttpResponse\Http;

use Mj4444\SimpleHttpClient\Contracts\HttpResponseInterface;

final class UnauthorizedException extends HttpException
{
    public function __construct(HttpResponseInterface $response)
    {
        parent::__construct($response, 'Unauthorized.');
    }
}
