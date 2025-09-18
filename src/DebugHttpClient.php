<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient;

use Mj4444\SimpleHttpClient\Contracts\HttpClientInterface;
use Mj4444\SimpleHttpClient\Contracts\HttpRequestInterface;
use Mj4444\SimpleHttpClient\Contracts\HttpResponseInterface;

final class DebugHttpClient implements HttpClientInterface
{
    public static ?HttpRequestInterface $globalLastRequest = null;
    public static ?HttpResponseInterface $globalLastResponse = null;
    public bool $isDebug = false;
    public static bool $isGlobalDebug = true;
    public ?HttpRequestInterface $lastRequest = null;
    public ?HttpResponseInterface $lastResponse = null;

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
        $this->lastRequest = $this->isDebug ? $request : null;
        $this->lastResponse = null;

        self::$globalLastRequest = self::$isGlobalDebug ? $request : null;
        self::$globalLastResponse = null;

        $response = $this->parent->request($request);


        if ($this->isDebug) {
            $this->lastResponse = $response;
        }
        if (self::$isGlobalDebug) {
            self::$globalLastResponse = $response;
        }

        return $response;
    }

    /**
     * @return $this
     */
    public function setDebug(bool $value): self
    {
        $this->isDebug = $value;

        return $this;
    }

    public static function setGlobalDebug(bool $value): void
    {
        self::$isGlobalDebug = $value;
    }
}
