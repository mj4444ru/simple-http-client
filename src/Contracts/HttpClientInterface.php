<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Contracts;

interface HttpClientInterface
{
    public function request(HttpRequestInterface $request): HttpResponseInterface;
}
