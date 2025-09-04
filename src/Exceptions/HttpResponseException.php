<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Exceptions;

use Mj4444\SimpleHttpClient\Contracts\HttpResponseInterface;
use Throwable;

abstract class HttpResponseException extends HttpClientException
{
    public function __construct(
        string $message,
        int $code,
        private readonly HttpResponseInterface $response,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getResponse(): HttpResponseInterface
    {
        return $this->response;
    }
}
