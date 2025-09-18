<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient;

use Closure;
use Mj4444\SimpleHttpClient\Contracts\HttpClientInterface;
use Mj4444\SimpleHttpClient\Contracts\HttpRequestInterface;
use Mj4444\SimpleHttpClient\Contracts\HttpResponseInterface;

final class MiddlewareHttpClient implements HttpClientInterface
{
    /**
     * @var (Closure(HttpRequestInterface, HttpClientInterface): HttpResponseInterface)|null
     */
    private ?Closure $middleware = null;

    public function __construct(
        private readonly HttpClientInterface $parent
    ) {
    }

    /**
     * @inheritDoc
     *
     * @template TResponse of HttpResponseInterface
     * @param HttpRequestInterface<TResponse> $request
     * @return TResponse
     */
    public function request(HttpRequestInterface $request): HttpResponseInterface
    {
        /**
         * @var TResponse $response
         */
        $response = $this->middleware
            ? ($this->middleware)($request, $this->parent)
            : $this->parent->request($request);

        return $response;
    }

    /**
     * @param (Closure(HttpRequestInterface, HttpClientInterface): HttpResponseInterface)|null $middleware
     * @return $this
     */
    public function setMiddleware(?Closure $middleware): self
    {
        $this->middleware = $middleware;

        return $this;
    }
}
