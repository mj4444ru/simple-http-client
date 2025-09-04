<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Exceptions\JsonHttpClient;

use Mj4444\SimpleHttpClient\Contracts\HttpResponseInterface;

final class ResponseNotJsonException extends ResponseException
{
    public function __construct(HttpResponseInterface $response)
    {
        parent::__construct('Response not json.', 0, $response);
    }
}
