<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Exceptions\JsonHttpClient;

use JsonException;

final class BodyNotValidJsonException extends RequestException
{
    public function __construct(
        private readonly mixed $body,
        ?JsonException $previous = null
    ) {
        parent::__construct('Not valid json.', 0, $previous);
    }

    public function getBody(): mixed
    {
        return $this->body;
    }
}
