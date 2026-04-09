<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Contracts;

/**
 * HTTP client interface for sending HTTP requests.
 */
interface HttpClientInterface
{
    /**
     * Sends an HTTP request and returns the response.
     *
     * @template TResponse of HttpResponseInterface
     * @param HttpRequestInterface<TResponse> $request The HTTP request to send.
     * @return TResponse The HTTP response.
     */
    public function request(HttpRequestInterface $request): HttpResponseInterface;
}
