<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Contracts;

interface HttpClientInterface
{
    /**
     * @template TResponse of HttpResponseInterface
     * @param HttpRequestInterface<TResponse> $request
     * @return TResponse
     */
    public function request(HttpRequestInterface $request): HttpResponseInterface;
}
