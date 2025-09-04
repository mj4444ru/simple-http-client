<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Exceptions\JsonHttpClient;

use JsonException;
use Mj4444\SimpleHttpClient\Contracts\HttpResponseInterface;

final class ResponseNotValidJsonException extends ResponseException
{
    public function __construct(HttpResponseInterface $response, ?JsonException $previous = null)
    {
        parent::__construct('Not valid json.', 0, $response, $previous);
    }
}
