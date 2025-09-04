<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Contracts;

use Mj4444\SimpleHttpClient\Exceptions\HttpClientException;
use Mj4444\SimpleHttpClient\Exceptions\HttpRequest\BodyRequiredException;

interface HttpClientInterface
{
    /**
     * @throws HttpClientException
     */
    public function request(HttpRequestInterface $request): HttpResponseInterface;
}
