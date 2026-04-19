<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Exceptions;

use Mj4444\SimpleHttpClient\Contracts\HttpRequestInterface;

/**
 * Base wrapper for external errors.
 */
abstract class HttpClientErrorException extends HttpClientException
{
    public function __construct(
        string $message,
        int $code,
        public readonly HttpRequestInterface $request
    ) {
        parent::__construct($message, $code);
    }

    public function getRequest(): HttpRequestInterface
    {
        return $this->request;
    }
}
